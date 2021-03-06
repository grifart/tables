<?php declare(strict_types=1);


namespace Grifart\Tables;


interface Row
{

	/**
	 * @param mixed[] $values
	 */
	public static function reconstitute(array $values): static;

}
