<?php declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator as Code;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Property;

final class ReconstituteConstructorDecorator implements ClassDecorator
{

	public function decorate(ClassType $classType, ClassDefinition $definition): void
	{
		$reconstitute = $classType->addMethod('reconstitute')
			->setReturnType('static')
			->setParameters([(new Code\Parameter('values'))->setTypeHint('array')])
			->setStatic();

		// todo use class definition instead when https://gitlab.grifart.cz/grifart/class-scaffolder/issues/5 is resolved
		$properties = \array_map(function(Property $property): string {return $property->getName();}, $classType->getProperties());
		$questionMarks = \implode(', ', array_fill(0, \count($properties), '?'));
		$literals = \array_map(function(string $property): Code\PhpLiteral {return new Code\PhpLiteral("\$values['" . $property . "']");}, $properties);

		// todo: add array_keys that it contains just keys that are necessary

		$reconstitute->addBody("return new static($questionMarks);", \array_values($literals));
	}
}
