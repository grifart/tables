<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Dibi\Expression as DibiExpression;
use Grifart\Tables\Expression;
use function Grifart\Tables\Types\mapToDatabase;
use function Phun\map;

/**
 * @template ValueType
 */
final class IsIn implements Condition
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
			'? IN %in',
			$this->expression->toSql(),
			map(
				$this->values,
				fn(mixed $value) => mapToDatabase($value, $this->expression->getType()),
			),
		);
	}
}
