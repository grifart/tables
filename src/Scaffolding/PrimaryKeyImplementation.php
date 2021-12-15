<?php

declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Field;
use Grifart\Tables\CaseConversion;
use Grifart\Tables\PrimaryKey;
use Grifart\Tables\Table;
use Nette\PhpGenerator\Literal;
use function Functional\map;

final class PrimaryKeyImplementation implements Capability
{
	public function __construct(
		private string $tableClassName,
		private string $rowClassName,
	) {}

	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$classType = $draft->getClassType();
		$namespace = $draft->getNamespace();

		$classType->setFinal();
		$classType->addImplement(PrimaryKey::class);
		$namespace->addUse(PrimaryKey::class);

		$namespace->addUse($this->tableClassName);
		$namespace->addUse($this->rowClassName);

		$fromRow = $classType->addMethod('fromRow')
			->setReturnType('self')
			->setStatic();

		$fromRow->addParameter('row')->setType($this->rowClassName);
		$fromRow->addBody('return self::from(...?);', [
			map(
				$definition->getFields(),
				static fn(Field $field) => new Literal(\sprintf('$row->get%s()', \ucfirst($field->getName()))),
			),
		]);

		$getQuery = $classType->addMethod('getQuery')
			->setReturnType('array')
			->addBody('$query = [];');

		$getQuery->addParameter('table')->setType(Table::class);
		$namespace->addUse(Table::class);

		foreach ($definition->getFields() as $field) {
			$getQuery->addBody('$query[?] = ?;', [
				new Literal('$table::?', [
					CaseConversion::toUnderscores($field->getName()),
				]),
				new Literal('$table->?()->map($this->?)', [
					$field->getName(),
					$field->getName(),
				]),
			]);
		}

		$getQuery->addBody('return $query;');
	}
}
