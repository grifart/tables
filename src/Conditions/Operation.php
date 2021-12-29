<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

/**
 * @template ValueType
 */
interface Operation
{
	public function getOperator(): string;

	public function hasOperand(): bool;

	/**
	 * @param \Closure(ValueType|null): mixed $mapper
	 */
	public function mapOperand(\Closure $mapper): mixed;
}
