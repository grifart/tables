<?php

declare(strict_types=1);

namespace Grifart\Tables\OrderBy;

use Grifart\Tables\Expression;

final class OrderBy
{
	public const ASC = 'ASC';
	public const DESC = 'DESC';

	/**
	 * @param Expression<mixed> $expression
	 * @param self::ASC|self::DESC $direction
	 */
	public function __construct(
		private Expression $expression,
		private string $direction = self::ASC,
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
