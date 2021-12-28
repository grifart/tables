<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Expression as DibiExpression;
use Dibi\Literal;
use Grifart\Tables\Conditions\Operation;
use Grifart\Tables\Conditions\SingleCondition;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\OrderBy\OrderByDirection;

/**
 * @template ValueType
 */
abstract class Expression
{
	abstract public function toSql(): DibiExpression|Literal;

	/**
	 * @return Type<ValueType>
	 */
	abstract public function getType(): Type;

	/**
	 * @param ValueType|null $value
	 */
	public function map(mixed $value): mixed
	{
		if ($value === null) {
			return null;
		}

		return $this->getType()->toDatabase($value);
	}

	/**
	 * @param Operation<ValueType> $operation
	 * @return SingleCondition<ValueType>
	 */
	public function is(Operation $operation): SingleCondition
	{
		return new SingleCondition($this, $operation);
	}

	public function ascending(): OrderBy
	{
		return new OrderBy($this);
	}

	public function descending(): OrderBy
	{
		return new OrderBy($this, OrderByDirection::DESC);
	}
}
