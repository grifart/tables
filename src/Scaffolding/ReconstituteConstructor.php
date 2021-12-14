<?php declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\PhpLiteral;

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
			->setStatic();

		$reconstitute->addParameter('values')->setType('array');

		$fields = $definition->getFields();
		$shapeFields = $literals = [];
		foreach ($fields as $field) {
			$name = $field->getName();
			$type = $field->getType();

			$shapeFields[] = \sprintf('%s: %s', $name, $type->getDocCommentType($draft->getNamespace()));
			$literals[] = new PhpLiteral("\$values['" . $name . "']");
		}

		$reconstitute->addComment(\sprintf('@param array{%s} $values', \implode(', ', $shapeFields)));
		$reconstitute->addBody("return new static(...?);", [$literals]);
	}
}
