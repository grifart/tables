<?php declare(strict_types=1);


namespace Grifart\Tables;


interface Row
{

	/** @return self */
	public static function reconstitute(array $values);

}
