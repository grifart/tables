<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Dibi\Expression as DibiExpression;
use Grifart\Tables\Expression;
use function Grifart\Tables\Types\mapToDatabase;

/**
 * @template ValueType
 */
final class IsNotEqualTo implements Condition
{
	/**
	 * @param Expression<ValueType> $expression
	 * @param ValueType $value
	 */
	public function __construct(
		private Expression $expression,
		private mixed $value,
	) {}

	public function toSql(): DibiExpression
	{
		return new DibiExpression(
			'? != ?',
			$this->expression->toSql(),
			mapToDatabase($this->value, $this->expression->getType()),
		);
	}
}
