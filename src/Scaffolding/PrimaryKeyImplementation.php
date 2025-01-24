<?php

declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Field;
use Grifart\Tables\Conditions\Composite;
use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\PrimaryKey;
use Grifart\Tables\Table;
use Nette\PhpGenerator\Literal;
use function Phun\map;

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
		$classType->addComment(\sprintf('@implements PrimaryKey<%s>', $namespace->simplifyName($this->tableClassName)));
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

		$namespace->addUse(Condition::class);
		$namespace->addUse(Composite::class);
		$namespace->addUseFunction('Grifart\Tables\Conditions\equalTo');

		$getCondition = $classType->addMethod('getCondition')->setReturnType(Condition::class);
		$getCondition->addParameter('table')->setType(Table::class);
		$namespace->addUse(Table::class);

		$getCondition->addBody('return Composite::and(');
		foreach ($definition->getFields() as $field) {
			$getCondition->addBody("\t\$table->?()->is(equalTo(\$this->?)),", [
				$field->getName(),
				$field->getName(),
			]);
		}

		$getCondition->addBody(');');
	}
}
