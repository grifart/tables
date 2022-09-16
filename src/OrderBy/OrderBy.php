<?php

declare(strict_types=1);

namespace Grifart\Tables\OrderBy;

use Dibi\Expression as DibiExpression;
use Grifart\Tables\Expression;

final class OrderBy
{
	private readonly Nulls $nulls;

	/**
	 * @param Expression<mixed> $expression
	 */
	public function __construct(
		private readonly Expression $expression,
		private readonly Direction $direction = Direction::Ascending,
		Nulls|null $nulls = null,
	)
	{
		$this->nulls = $nulls ?? Nulls::default($this->direction);
	}

	public function toSql(): DibiExpression
	{
		return new DibiExpression(
			'? %ex %ex',
			$this->expression->toSql(),
			$this->direction->toSql(),
			$this->nulls->toSql(),
		);
	}
}
