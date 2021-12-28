<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

/**
 * @template ValueType
 */
interface Condition
{
	/**
	 * @return mixed[] the WHERE part of a Dibi query
	 */
	public function format(): array;
}
