<?php declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\Tables\Row;
use Grifart\Tables\TypeMapper;
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
		string $primaryKeyClass
	): Definitions
	{
		$columnsNativeTypes = $pgReflector->retrieveColumnInfo($schema, $table);
		if (\count($columnsNativeTypes) === 0) {
			throw new \LogicException('No columns found for given configuration. Does referenced table exist?');
		}

		$location = function(string $column) use ($schema, $table): string {
			return self::location($schema, $table, $column);
		};

		$columnsPhpTypes = [];
		foreach ($columnsNativeTypes as $column) {
			$phpType = resolve($mapper->mapType($location($column->getName()), $column->getType()));
			$columnsPhpTypes[$column->getName()] = $column->isNullable() ? nullable($phpType) : $phpType;
		}

		$addTableFields = function (ClassDefinition $definition) use ($columnsPhpTypes): ClassDefinition {
			return $definition->withFields($columnsPhpTypes);
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
			->with(new ModificationsImplementation($tableClassName, $primaryKeyClass));

		// table class
		$tableClass = (new ClassDefinition($tableClassName))
			->with(new TableImplementation(
				$schema,
				$table,
				$primaryKeyClass,
				$rowClassName,
				$modificationsClassName,
				$columnsNativeTypes,
				$columnsPhpTypes,
			));

		return Definitions::from($rowClass, $modificationsClass, $tableClass);
	}

}
