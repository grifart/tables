<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Expression as DibiExpression;
use Dibi\Literal;
use function Functional\map;

/**
 * @template ValueType
 * @param Type<ValueType> $type
 * @return Expression<ValueType>
 */
function expr(
	Type $type,
	string $pattern,
	mixed ...$arguments,
): Expression
{
	return new /** @extends Expression<ValueType> */ class($type, $pattern, $arguments) extends Expression {
		/**
		 * @param Type<ValueType> $type
		 * @param mixed[] $arguments
		 */
		public function __construct(
			private Type $type,
			private string $pattern,
			private array $arguments,
		) {}

		public function toSql(): DibiExpression|Literal
		{
			return new DibiExpression(
				$this->pattern,
				...map(
					$this->arguments,
					static fn(mixed $argument) => $argument instanceof Expression ? $argument->toSql() : $argument,
				),
			);
		}

		public function getType(): Type
		{
			return $this->type;
		}
	};
}
