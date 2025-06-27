<?php

declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\Capabilities\CapabilityTools;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

final class DeprecatedGetters implements Capability
{
	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$classType = $draft->getClassType();
		CapabilityTools::checkIfAllFieldsArePresent($definition, $classType);

		foreach ($definition->getFields() as $field) {
			$fieldName = $field->getName();
			$getterName = 'get' . \ucfirst($fieldName);

			if ( ! $classType->hasMethod($getterName)) {
				continue;
			}

			$getter = $classType->getMethod($getterName);
			$getter->addAttribute(\Deprecated::class, [\sprintf('Use $%s property instead.', $fieldName)]);
		}
	}
}
