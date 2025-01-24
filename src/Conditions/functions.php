<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Grifart\Tables\Expression;

function all(Condition ...$conditions): Composite {
	return Composite::and(...$conditions);
}

function any(Condition ...$conditions): Composite {
	return Composite::or(...$conditions);
}


/**
 * @template ValueType
 * @param ValueType $value
 * @return \Closure(Expression<ValueType>): IsEqualTo<ValueType>
 */
function equalTo(mixed $value): \Closure
{
	return static fn(Expression $expression) => new IsEqualTo($expression, $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return \Closure(Expression<ValueType>): IsGreaterThan<ValueType>
 */
function greaterThan(mixed $value): \Closure
{
	return static fn(Expression $expression) => new IsGreaterThan($expression, $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return \Closure(Expression<ValueType>): IsGreaterThanOrEqualTo<ValueType>
 */
function greaterThanOrEqualTo(mixed $value): \Closure
{
	return static fn(Expression $expression) => new IsGreaterThanOrEqualTo($expression, $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return \Closure(Expression<ValueType>): IsLesserThan<ValueType>
 */
function lesserThan(mixed $value): \Closure
{
	return static fn(Expression $expression) => new IsLesserThan($expression, $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return \Closure(Expression<ValueType>): IsLesserThanOrEqualTo<ValueType>
 */
function lesserThanOrEqualTo(mixed $value): \Closure
{
	return static fn(Expression $expression) => new IsLesserThanOrEqualTo($expression, $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return \Closure(Expression<ValueType>): IsNotEqualTo<ValueType>
 */
function notEqualTo(mixed $value): \Closure
{
	return static fn(Expression $expression) => new IsNotEqualTo($expression, $value);
}

/**
 * @template ValueType
 * @param ValueType[] $values
 * @return \Closure(Expression<ValueType>): IsIn<ValueType>
 */
function in(array $values): \Closure {
	return static fn(Expression $expression) => new IsIn($expression, $values);
}

/**
 * @template ValueType
 * @param ValueType[] $values
 * @return \Closure(Expression<ValueType>): IsNotIn<ValueType>
 */
function notIn(array $values): \Closure {
	return static fn(Expression $expression) => new IsNotIn($expression, $values);
}

/**
 * @return \Closure(Expression<*>): IsNull
 */
function null(): \Closure
{
	return static fn(Expression $expression) => new IsNull($expression);
}

/**
 * @return \Closure(Expression<*>): IsNotNull
 */
function notNull(): \Closure
{
	return static fn(Expression $expression) => new IsNotNull($expression);
}
