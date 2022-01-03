<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Expression as DibiExpression;

/**
 * @template ValueType
 */
interface Expression
{
	public function toSql(): DibiExpression;

	/**
	 * @return Type<ValueType>
	 */
	public function getType(): Type;
}
