<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Dibi\Expression as DibiExpression;
use Grifart\Tables\Expression;
use function Phun\map;

/**
 * @template ValueType
 */
final class IsNotIn implements Condition
{
	/**
	 * @param Expression<ValueType> $expression
	 * @param ValueType[] $values
	 */
	public function __construct(
		private Expression $expression,
		private array $values,
	) {}

	public function toSql(): DibiExpression
	{
		return new DibiExpression(
			'? NOT IN %in',
			$this->expression->toSql(),
			map(
				$this->values,
				fn(mixed $value) => $this->expression->getType()->toDatabase($value),
			),
		);
	}
}
