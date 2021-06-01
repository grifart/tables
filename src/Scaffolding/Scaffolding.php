<?php declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Decorators\GettersDecorator;
use Grifart\ClassScaffolder\Decorators\InitializingConstructorDecorator;
use Grifart\ClassScaffolder\Decorators\PropertiesDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;
use Grifart\Tables\Row;
use Grifart\Tables\TypeMapper;
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
	): Builders
	{
		return self::buildersForPgTable(
			$pgReflector,
			$mapper,
			$schema,
			$table,
			$rowClassName,
			$modificationsClassName,
			$tableClassName,
			$primaryKeyClass,
		);
	}


	/**
	 * Usage:
	 * ```php
	 * $builders = Scaffolding::buildersForPgTable(...);
	 * $builders->getRowClass()->decorate(...);
	 * return $builders;
	 * ```
	 */
	public static function buildersForPgTable(
		PostgresReflector $pgReflector,
		TypeMapper $mapper,
		string $schema,
		string $tableClass,
		string $rowClassName,
		string $modificationsClassName,
		string $tableClassName,
		string $primaryKeyClass
	): Builders
	{
		$columnsNativeTypes = $pgReflector->retrieveColumnInfo($schema, $tableClass);
		if (\count($columnsNativeTypes) === 0) {
			throw new \LogicException('No columns found for given configuration. Does referenced table exist?');
		}

		$location = function(string $column) use ($schema, $tableClass): string {
			return self::location($schema, $tableClass, $column);
		};

		$columnsPhpTypes = [];
		foreach ($columnsNativeTypes as $column) {
			$phpType = resolve($mapper->mapType($location($column->getName()), $column->getType()));
			$columnsPhpTypes[$column->getName()] = $column->isNullable() ? nullable($phpType) : $phpType;
		}

		$addTableFields = function (ClassDefinitionBuilder $builder) use ($columnsPhpTypes): ClassDefinitionBuilder {
			foreach ($columnsPhpTypes as $name => $type) {
				$builder->field($name, $type);
			}
			return $builder;
		};


		// row class
		$rowClass = $addTableFields(new ClassDefinitionBuilder($rowClassName))
			->implement(Row::class)
			->decorate(new PropertiesDecorator())
			->decorate(new InitializingConstructorDecorator())
			->decorate(new GettersDecorator())
			->decorate(new PrivateConstructorDecorator())
			->decorate(new ReconstituteConstructorDecorator());

		// row modification class
		$modificationsClass = $addTableFields(new ClassDefinitionBuilder($modificationsClassName))
			->decorate(new ModificationsDecorator($tableClassName, $primaryKeyClass));

		// table class
		$tableClass = (new ClassDefinitionBuilder($tableClassName))
			->decorate(new TableDecorator(
				$schema,
				$tableClass,
				$primaryKeyClass,
				$rowClassName,
				$modificationsClassName,
				$columnsNativeTypes,
				$columnsPhpTypes,
			));

		return Builders::from($rowClass, $modificationsClass, $tableClass);
	}

}
