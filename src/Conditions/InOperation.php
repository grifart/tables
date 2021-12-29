<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use function Functional\map;

/**
 * @template ValueType
 * @implements Operation<ValueType>
 */
final class InOperation implements Operation
{
	/**
	 * @param ValueType[] $values
	 */
	public function __construct(
		private array $values,
		private bool $negated = false,
	) {}

	public function getOperator(): string
	{
		return $this->negated ? 'NOT IN' : 'IN';
	}

	public function hasOperand(): bool
	{
		return true;
	}

	public function mapOperand(\Closure $mapper): mixed
	{
		return map($this->values, $mapper);
	}
}
