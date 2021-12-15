<?php declare(strict_types=1);


namespace Grifart\Tables;

/**
 * @template TableType of Table
 */
interface PrimaryKey
{

	/**
	 * @param TableType $table
	 * @return array<string, mixed> query used in WHERE to narrow down results into one record
	 */
	public function getQuery(Table $table): array;

	/** @return static */
	//public static function fromRow($row);

}
