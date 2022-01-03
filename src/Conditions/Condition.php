<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use Dibi\Expression as DibiExpression;

interface Condition
{
	/**
	 * @return DibiExpression the WHERE part of a Dibi query
	 */
	public function toSql(): DibiExpression;
}
