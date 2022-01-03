<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Grifart\Tables\Expression;
use function Functional\map;
use function Grifart\Tables\Types\mapToDatabase;

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

	public function format(): array
	{
		return [
			'? IN %in',
			$this->expression->toSql(),
			map(
				$this->values,
				fn(mixed $value) => mapToDatabase($value, $this->expression->getType()),
			),
		];
	}
}
