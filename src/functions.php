<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Expression as DibiExpression;
use function Functional\map;

/**
 * @template ValueType
 * @param Type<ValueType> $type
 * @return ExpressionWithShorthands<ValueType>
 */
function expr(
	Type $type,
	string $pattern,
	mixed ...$arguments,
): ExpressionWithShorthands
{
	return new /** @extends ExpressionWithShorthands<ValueType> */ class($type, $pattern, $arguments) extends ExpressionWithShorthands {
		/**
		 * @param Type<ValueType> $type
		 * @param mixed[] $arguments
		 */
		public function __construct(
			private Type $type,
			private string $pattern,
			private array $arguments,
		) {}

		public function toSql(): DibiExpression
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
