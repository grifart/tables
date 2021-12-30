<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Grifart\Tables\Expression;
use function Grifart\Tables\Types\mapToDatabase;

/**
 * @template ValueType
 */
final class IsGreaterThanOrEqualTo implements Condition
{
	/**
	 * @param Expression<ValueType> $expression
	 * @param ValueType $value
	 */
	public function __construct(
		private Expression $expression,
		private mixed $value,
	) {}

	public function format(): array
	{
		return [
			'? >= ?',
			$this->expression->toSql(),
			mapToDatabase($this->value, $this->expression->getType()),
		];
	}
}
