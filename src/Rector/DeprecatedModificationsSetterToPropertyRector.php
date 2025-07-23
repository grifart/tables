<?php

declare(strict_types=1);

namespace Grifart\Tables\Rector;

use Grifart\Tables\Modifications;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use function lcfirst;
use function str_starts_with;
use function strlen;
use function substr;

final class DeprecatedModificationsSetterToPropertyRector extends AbstractRector
{
	private const string METHOD_NAME_PREFIX = 'modify';

	/**
	 * @return array<class-string<Node>>
	 */
	public function getNodeTypes(): array
	{
		return [Node\Expr\MethodCall::class];
	}

	/**
	 * @param Node\Expr\MethodCall $node
	 */
	public function refactor(Node $node): ?Node
	{
		$methodCallName = $this->getName($node->name);
		if ($methodCallName === null) {
			return null;
		}

		if (!str_starts_with($methodCallName, self::METHOD_NAME_PREFIX)) {
			return null;
		}

		if (!$this->nodeTypeResolver->isObjectType($node->var, new ObjectType(Modifications::class))) {
			return null;
		}

		$propertyName = lcfirst(substr($methodCallName, strlen(self::METHOD_NAME_PREFIX)));
		$propertyFetch = new Node\Expr\PropertyFetch($node->var, new Node\Identifier($propertyName));

		$arg = $node->args[0];
		if ($arg instanceof Node\VariadicPlaceholder) {
			return null;
		}

		return new Node\Expr\Assign($propertyFetch, $arg->value);
	}
}
