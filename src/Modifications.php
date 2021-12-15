<?php declare(strict_types=1);


namespace Grifart\Tables;

/**
 * @template TableType of Table
 */
interface Modifications
{


	/**
	 * @internal used by {@see AccountsTable}
	 * @return mixed[]
	 */
	public function getModifications(): array;

	/**
	 * @return null|PrimaryKey<TableType> if null it means, that row is new (do INSERT)
	 */
	public function getPrimaryKey(): ?PrimaryKey;

	/**
	 * With which table is this row associated
	 * @return class-string<TableType>
	 */
	public static function forTable(): string;

}
