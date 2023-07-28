<?php

declare(strict_types=1);

namespace Grifart\Tables\OrderBy;

use Dibi\Expression as DibiExpression;
use Grifart\Tables\Expression;
use Grifart\Tables\Types\ArrayType;

/**
 * @template ValueType
 */
final class OrderByValues implements OrderBy
{
	/**
	 * @param Expression<ValueType> $expression
	 * @param ValueType[] $values
	 */
	public function __construct(
		private readonly Expression $expression,
		private readonly array $values,
	) {}

	public function toSql(): DibiExpression
	{
		return new DibiExpression(
			'array_position(?, ?)',
			(ArrayType::of($this->expression->getType()))->toDatabase($this->values),
			$this->expression->toSql(),
		);
	}
}
