<?php

declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\ColumnMetadata;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Row;
use Grifart\Tables\Type;
use Grifart\Tables\TypeResolver;
use function array_keys;
use function Functional\map;
use function Grifart\ClassScaffolder\Capabilities\constructorWithPromotedProperties;
use function Grifart\ClassScaffolder\Capabilities\getters;
use function Grifart\ClassScaffolder\Capabilities\implementedInterface;
use function Grifart\ClassScaffolder\Capabilities\namedConstructor;
use function Grifart\ClassScaffolder\Capabilities\privatizedConstructor;
use function Grifart\ClassScaffolder\Definition\Types\nullable;

final class TablesDefinitions
{
	public function __construct(
		private PostgresReflector $pgReflector,
		private TypeResolver $typeResolver,
	) {}

	public function for(
		string $schema,
		string $table,
		string $rowClassName,
		string $modificationsClassName,
		string $tableClassName,
		string $primaryKeyClassName,
	): Definitions
	{
		$columnMetadata = $this->pgReflector->retrieveColumnMetadata($schema, $table);
		if (\count($columnMetadata) === 0) {
			throw new \LogicException('No columns found for given configuration. Does referenced table exist?');
		}

		$columnResolvedTypes = map(
			$columnMetadata,
			function (ColumnMetadata $column) use ($schema, $table): Type {
				$location = new Identifier($schema, $table, $column->getName());
				return $this->typeResolver->resolveType($column->getType(), $location);
			},
		);

		$columnPhpTypes = map(
			$columnMetadata,
			static function (ColumnMetadata $column) use ($columnResolvedTypes): PhpType {
				$type = $columnResolvedTypes[$column->getName()];
				$phpType = $type->getPhpType();
				return $column->isNullable() ? nullable($phpType) : $phpType;
			},
		);

		$addTableFields = function (ClassDefinition $definition) use ($columnPhpTypes): ClassDefinition {
			return $definition->withFields($columnPhpTypes);
		};


		// row class
		$rowClass = $addTableFields(new ClassDefinition($rowClassName))
			->with(
				implementedInterface(Row::class),
				constructorWithPromotedProperties(),
				privatizedConstructor(),
				getters(),
				new ReconstituteConstructor(),
			);

		// row modification class
		$modificationsClass = $addTableFields(new ClassDefinition($modificationsClassName))
			->with(new ModificationsImplementation($tableClassName, $primaryKeyClassName, $columnMetadata));

		$primaryKeyColumnNames = $this->pgReflector->retrievePrimaryKeyColumns($schema, $table);
		$primaryKeyFields = map($primaryKeyColumnNames, static fn(string $name) => $columnPhpTypes[$name]);
		$primaryKeyClass = (new ClassDefinition($primaryKeyClassName))
			->withFields($primaryKeyFields)
			->with(
				constructorWithPromotedProperties(),
				privatizedConstructor(),
				namedConstructor('from'),
				new PrimaryKeyImplementation($tableClassName, $rowClassName),
				getters(),
			);

		// table class
		$tableClass = (new ClassDefinition($tableClassName))
			->with(new TableImplementation(
				$schema,
				$table,
				$primaryKeyClassName,
				$rowClassName,
				$modificationsClassName,
				$columnMetadata,
				$columnPhpTypes,
			));

		return Definitions::from($rowClass, $modificationsClass, $primaryKeyClass, $tableClass);
	}
}
