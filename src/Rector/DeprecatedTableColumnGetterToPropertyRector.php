<?php

declare(strict_types=1);

namespace Grifart\Tables\Rector;

use Grifart\Tables\Table;use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use function in_array;

final class DeprecatedTableColumnGetterToPropertyRector extends AbstractRector
{
	/** @var list<string> */
	private array $knownMethodNames = [
		'getSchema',
		'getTableName',
		'getPrimaryKeyClass',
		'getRowClass',
		'getModificationClass',
		'getDatabaseColumns',
		'getTypeOf',
		'find',
		'get',
		'getAll',
		'findBy',
		'count',
		'getUniqueBy',
		'findUniqueBy',
		'getFirstBy',
		'findFirstBy',
		'save',
		'new',
		'edit',
		'insert',
		'insertAndGet',
		'update',
		'updateAndGet',
		'updateBy',
		'upsert',
		'upsertAndGet',
		'delete',
		'deleteAndGet',
		'deleteBy',
	];

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

		if (!$this->nodeTypeResolver->isObjectType($node->var, new ObjectType(Table::class))) {
			return null;
		}

		if (in_array($methodCallName, $this->knownMethodNames, true)) {
			return null;
		}

		return $this->nodeFactory->createPropertyFetch($node->var, $methodCallName);
	}
}
