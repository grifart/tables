<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Dibi\Expression as DibiExpression;
use Grifart\Tables\Expression;

final class IsNotNull implements Condition
{
	/**
	 * @param Expression<mixed> $expression
	 */
	public function __construct(
		private Expression $expression,
	) {}

	public function toSql(): DibiExpression
	{
		return new DibiExpression(
			'? IS NOT NULL',
			$this->expression->toSql(),
		);
	}
}
