<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Expression as DibiExpression;
use Dibi\Literal;

/**
 * @template ValueType
 */
interface Expression
{
	public function toSql(): DibiExpression|Literal;

	/**
	 * @return Type<ValueType>
	 */
	public function getType(): Type;
}
