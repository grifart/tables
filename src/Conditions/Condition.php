<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

/**
 * @template ValueType
 */
interface Condition
{
	/**
	 * @return mixed[]
	 */
	public function format(): array;
}
