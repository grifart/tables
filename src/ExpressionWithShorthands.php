<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\OrderBy\OrderByDirection;

/**
 * @template ValueType
 * @implements Expression<ValueType>
 */
abstract class ExpressionWithShorthands implements Expression
{
	/**
	 * @param \Closure(Expression<ValueType>): Condition $conditionFactory
	 */
	public function is(\Closure $conditionFactory): Condition
	{
		return $conditionFactory($this);
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
