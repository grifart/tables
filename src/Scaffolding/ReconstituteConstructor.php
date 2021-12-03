<?php declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Field;
use Nette\PhpGenerator as Code;

final class ReconstituteConstructor implements Capability
{
	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$classType = $draft->getClassType();

		$reconstitute = $classType->addMethod('reconstitute')
			->setReturnType('static')
			->setParameters([(new Code\Parameter('values'))->setTypeHint('array')])
			->setStatic();

		$fields = $definition->getFields();
		$questionMarks = \implode(', ', array_fill(0, \count($fields), '?'));
		$literals = \array_map(function(Field $field): Code\PhpLiteral {return new Code\PhpLiteral("\$values['" . $field->getName() . "']");}, $fields);

		$reconstitute->addBody("return new static($questionMarks);", \array_values($literals));
	}
}
