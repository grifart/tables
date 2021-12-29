<?php

declare(strict_types=1);

namespace Grifart\Tables\OrderBy;

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

	/**
	 * @return mixed[]
	 */
	public function format(): array
	{
		return [
			'? %sql',
			$this->expression->toSql(),
			$this->direction,
		];
	}
}
