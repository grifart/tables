<?php

declare(strict_types=1);

namespace Grifart\Tables\OrderBy;

use Dibi\Expression as DibiExpression;
use Grifart\Tables\Expression;

final class OrderBy
{
	/**
	 * @param Expression<mixed> $expression
	 * @param OrderByDirection::ASC|OrderByDirection::DESC $direction
	 */
	public function __construct(
		private Expression $expression,
		private string $direction = OrderByDirection::ASC,
	) {}

	public function toSql(): DibiExpression
	{
		return new DibiExpression(
			'? %sql',
			$this->expression->toSql(),
			$this->direction,
		);
	}
}
