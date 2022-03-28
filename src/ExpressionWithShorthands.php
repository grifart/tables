<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\Conditions\IsEqualTo;
use Grifart\Tables\Conditions\IsNull;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\OrderBy\OrderByDirection;

/**
 * @template ValueType
 * @implements Expression<ValueType>
 */
abstract class ExpressionWithShorthands implements Expression
{
	/**
	 * @param (\Closure(Expression<ValueType>): Condition)|ValueType|null $conditionFactory
	 */
	public function is(mixed $conditionFactory): Condition
	{
		if ($conditionFactory instanceof \Closure) {
			return $conditionFactory($this);
		}

		if ($conditionFactory === null) {
			return new IsNull($this);
		}

		return new IsEqualTo($this, $conditionFactory);
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
