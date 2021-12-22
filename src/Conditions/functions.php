<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Grifart\Tables\Expression;

/**
 * @template ValueType
 * @param Expression<ValueType> $expression
 * @param Operation<ValueType> $operation
 * @return SingleCondition<ValueType>
 */
function condition(
	Expression $expression,
	Operation $operation,
): SingleCondition {
	return new SingleCondition(
		$expression,
		$operation,
	);
}

/**
 * @param Condition<mixed> ...$conditions
 */
function all(Condition ...$conditions): CompositeCondition {
	return CompositeCondition::and(...$conditions);
}

/**
 * @param Condition<mixed> ...$conditions
 */
function any(Condition ...$conditions): CompositeCondition {
	return CompositeCondition::or(...$conditions);
}


/**
 * @template ValueType
 * @param ValueType $value
 * @return Operation<ValueType>
 */
function equalTo(mixed $value): Operation
{
	return new BinaryOperation('=', $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return Operation<ValueType>
 */
function greaterThan(mixed $value): Operation
{
	return new BinaryOperation('>', $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return Operation<ValueType>
 */
function greaterThanOrEqualTo(mixed $value): Operation
{
	return new BinaryOperation('>=', $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return Operation<ValueType>
 */
function lesserThan(mixed $value): Operation
{
	return new BinaryOperation('<', $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return Operation<ValueType>
 */
function lesserThanOrEqualTo(mixed $value): Operation
{
	return new BinaryOperation('<=', $value);
}

/**
 * @template ValueType
 * @param ValueType $value
 * @return Operation<ValueType>
 */
function notEqualTo(mixed $value): Operation
{
	return new BinaryOperation('!=', $value);
}

/**
 * @template ValueType
 * @param ValueType[] $values
 * @return Operation<ValueType>
 */
function in(array $values): Operation {
	return new InOperation($values);
}

/**
 * @template ValueType
 * @param ValueType[] $values
 * @return Operation<ValueType>
 */
function notIn(array $values): Operation {
	return new InOperation($values, negated: true);
}

/**
 * @return Operation<never>
 */
function null(): Operation
{
	return new NullOperation();
}

/**
 * @return Operation<never>
 */
function notNull(): Operation
{
	return new NullOperation(negated: true);
}
