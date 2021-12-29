<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

/**
 * @template ValueType
 * @implements Operation<ValueType>
 */
final class BinaryOperation implements Operation
{
	/**
	 * @param ValueType $value
	 */
	public function __construct(
		private string $operator,
		private mixed $value,
	) {}

	public function getOperator(): string
	{
		return $this->operator;
	}

	public function hasOperand(): bool
	{
		return true;
	}

	public function mapOperand(\Closure $mapper): mixed
	{
		return $mapper($this->value);
	}
}
