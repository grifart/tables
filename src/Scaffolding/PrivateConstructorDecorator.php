<?php declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;

final class PrivateConstructorDecorator implements ClassDecorator
{

	public function decorate(ClassType $classType, ClassDefinition $definition): void
	{
		$classType->getMethod('__construct')->setVisibility('private');
	}
}
