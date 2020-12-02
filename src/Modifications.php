<?php declare(strict_types=1);


namespace Grifart\Tables;


interface Modifications
{


	/**
	 * @internal used by {@see AccountsTable}
	 * @return mixed[]
	 */
	public function getModifications(): array;

	/** @return null|PrimaryKey if null it means, that row is new (do INSERT) */
	public function getPrimaryKey(): ?PrimaryKey;

	/** With which table is this row associated */
	public static function forTable(): string;

}
