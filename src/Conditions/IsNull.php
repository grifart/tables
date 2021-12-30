<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Grifart\Tables\Expression;

final class IsNull implements Condition
{
	/**
	 * @param Expression<mixed> $expression
	 */
	public function __construct(
		private Expression $expression,
	) {}

	public function format(): array
	{
		return [
			'? IS NULL',
			$this->expression->toSql(),
		];
	}
}
