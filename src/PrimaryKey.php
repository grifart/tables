<?php declare(strict_types=1);


namespace Grifart\Tables;

interface PrimaryKey
{

	/**
	 * @return array query used in WHERE to narrow down results into one record
	 */
	public function getQuery(): array;

	/** @return static */
	//public static function fromRow($row);

}
