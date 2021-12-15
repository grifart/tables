<?php declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types\Type;
use Grifart\Tables\ColumnMetadata;
use Grifart\Tables\Row;
use Grifart\Tables\TypeMapper;
use function Functional\map;
use function Grifart\ClassScaffolder\Capabilities\constructorWithPromotedProperties;
use function Grifart\ClassScaffolder\Capabilities\getters;
use function Grifart\ClassScaffolder\Capabilities\implementedInterface;
use function Grifart\ClassScaffolder\Capabilities\privatizedConstructor;
use function Grifart\ClassScaffolder\Definition\Types\nullable;
use function Grifart\ClassScaffolder\Definition\Types\resolve;


final class Scaffolding
{

	private static function location(string $schema, string $table, string $column): string
	{
		return "$schema.$table.$column";
	}


	/**
	 * Usage:
	 * ```php
	 * return Scaffolding::definitionsForPgTable(...);
	 * ```
	 */
	public static function definitionsForPgTable(
		PostgresReflector $pgReflector,
		TypeMapper $mapper,
		string $schema,
		string $table,
		string $rowClassName,
		string $modificationsClassName,
		string $tableClassName,
		string $primaryKeyClassName,
	): Definitions
	{
		$columnMetadata = $pgReflector->retrieveColumnMetadata($schema, $table);
		if (\count($columnMetadata) === 0) {
			throw new \LogicException('No columns found for given configuration. Does referenced table exist?');
		}

		$location = function(string $column) use ($schema, $table): string {
			return self::location($schema, $table, $column);
		};

		$columnPhpTypes = map(
			$columnMetadata,
			function (ColumnMetadata $column) use ($mapper, $location): Type {
				$phpType = resolve($mapper->mapType($location($column->getName()), $column->getType()));
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
			->with(new ModificationsImplementation($tableClassName, $primaryKeyClassName));

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

		return Definitions::from($rowClass, $modificationsClass, $tableClass);
	}

}
