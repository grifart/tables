<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Grifart\Tables\Conditions\Operation;
use Grifart\Tables\Conditions\SingleCondition;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\OrderBy\OrderByDirection;

/**
 * @template ValueType
 * @implements Expression<ValueType>
 */
abstract class ExpressionWithShorthands implements Expression
{
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
