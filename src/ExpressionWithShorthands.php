<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\Conditions\IsEqualTo;
use Grifart\Tables\Conditions\IsNull;
use Grifart\Tables\OrderBy\Direction;
use Grifart\Tables\OrderBy\Nulls;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\OrderBy\OrderByDirection;
use Grifart\Tables\OrderBy\OrderByValues;

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

	public function ascending(Nulls|null $nulls = null): OrderBy
	{
		return new OrderByDirection($this, nulls: $nulls);
	}

	public function descending(Nulls|null $nulls = null): OrderBy
	{
		return new OrderByDirection($this, direction: Direction::Descending, nulls: $nulls);
	}

	/**
	 * @param list<ValueType> $values
	 */
	public function byValues(array $values): OrderBy
	{
		return new OrderByValues($this, $values);
	}
}
