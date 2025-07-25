<?php

declare(strict_types=1);

namespace Grifart\Tables\Rector;

use Grifart\Tables\Row;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use function lcfirst;
use function str_starts_with;
use function strlen;
use function substr;

final class DeprecatedRowGetterToPropertyRector extends AbstractRector
{
	private const string METHOD_NAME_PREFIX = 'get';

	/**
	 * @return array<class-string<Node>>
	 */
	public function getNodeTypes(): array
	{
		return [
			Node\Expr\MethodCall::class,
			Node\Expr\NullsafeMethodCall::class,
		];
	}

	/**
	 * @param Node\Expr\MethodCall|Node\Expr\NullsafeMethodCall $node
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

		if (!$this->nodeTypeResolver->isObjectType($node->var, new ObjectType(Row::class))) {
			return null;
		}

		$propertyName = lcfirst(substr($methodCallName, strlen(self::METHOD_NAME_PREFIX)));
		return $node instanceof Node\Expr\NullsafeMethodCall
			? new Node\Expr\NullsafePropertyFetch($node->var, new Node\Identifier($propertyName))
			: new Node\Expr\PropertyFetch($node->var, new Node\Identifier($propertyName));
	}
}
