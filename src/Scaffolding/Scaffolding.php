<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;

/** @var \Nette\DI\Container $container */

use Grifart\ClassScaffolder\Decorators\GettersDecorator;
use Grifart\ClassScaffolder\Decorators\InitializingConstructorDecorator;
use Grifart\ClassScaffolder\Decorators\PropertiesDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;
use Grifart\ClassScaffolder\Definition\Types\Type;
use Grifart\Tables\Row;
use Grifart\Tables\TypeMapper;
use function Grifart\ClassScaffolder\Definition\Types\nullable;
use function Grifart\ClassScaffolder\Definition\Types\resolve;


final class Scaffolding
{

	private static function location(string $schema, string $table, string $column): string {
		return "$schema.$table.$column";
	}

	public static function definitionsForPgTable(
		PostgresReflector $pgReflector,
		TypeMapper $mapper,
		string $schema,
		string $table,
		string $rowClass,
		string $modificationClass,
		string $tableClass,
		string $primaryKeyClass
	): array {

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

		$addTableFields = function (ClassDefinitionBuilder $builder) use ($columnsPhpTypes): ClassDefinitionBuilder {
			foreach ($columnsPhpTypes as $name => $type) {
				$builder->field($name, $type);
			}
			return $builder;
		};

		return [
			// row class
			$addTableFields(new ClassDefinitionBuilder($rowClass))
				->implement(Row::class)
				->decorate(new PropertiesDecorator())
				->decorate(new InitializingConstructorDecorator())
				->decorate(new GettersDecorator())
				->decorate(new PrivateConstructorDecorator())
				->decorate(new ReconstituteConstructorDecorator())
				->build(),

			// row modification class
			$addTableFields(new ClassDefinitionBuilder($modificationClass))
				->decorate(new ModificationsDecorator($tableClass, $primaryKeyClass, $columnsNativeTypes))
				->build(),

			// table class
			(new ClassDefinitionBuilder($tableClass))
				->decorate(new TableDecorator(
					$schema,
					$table,
					$primaryKeyClass,
					$rowClass,
					$modificationClass,
					$columnsNativeTypes,
					$columnsPhpTypes,
				))
				->build(),

		];
	}

}
