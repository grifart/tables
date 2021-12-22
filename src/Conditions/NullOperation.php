<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

/**
 * @implements Operation<never>
 */
final class NullOperation implements Operation
{
	public function __construct(
		private bool $negated = false,
	) {}

	public function getOperator(): string
	{
		return $this->negated ? 'IS NOT NULL' : 'IS NULL';
	}

	public function hasOperand(): bool
	{
		return false;
	}

	public function mapOperand(\Closure $mapper): mixed
	{
		return null;
	}
}
