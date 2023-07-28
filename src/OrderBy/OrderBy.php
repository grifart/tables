<?php

declare(strict_types=1);

namespace Grifart\Tables\OrderBy;

use Dibi\Expression as DibiExpression;

interface OrderBy
{
	/**
	 * @return DibiExpression the ORDER BY part of a Dibi query
	 */
	public function toSql(): DibiExpression;
}
