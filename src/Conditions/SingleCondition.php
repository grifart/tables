<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Dibi\Driver;
use Grifart\Tables\Expression;

/**
 * @template ValueType
 * @implements Condition<ValueType>
 */
final class SingleCondition implements Condition
{
	/**
	 * @param Expression<ValueType> $expression
	 * @param Operation<ValueType> $operation
	 */
	public function __construct(
		private Expression $expression,
		private Operation $operation,
	) {}

	public function format(): array
	{
		if ($this->operation->hasOperand()) {
			return [
				\sprintf('? %s ?', $this->operation->getOperator()),
				$this->expression->toSql(),
				$this->operation->mapOperand(fn(mixed $value) => $value !== null ? $this->expression->getType()->toDatabase($value) : null),
			];
		}

		return [
			\sprintf('? %s', $this->operation->getOperator()),
			$this->expression->toSql(),
		];
	}
}
