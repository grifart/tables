<?php declare(strict_types=1);


namespace Grifart\Tables;

use Grifart\Tables\Conditions\Condition;

/**
 * @template TableType of Table
 */
interface PrimaryKey
{

	/**
	 * @param TableType $table
	 * @return Condition<mixed>[]
	 */
	public function getConditions(Table $table): array;

	/** @return static */
	//public static function fromRow($row);

}
