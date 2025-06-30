<?php declare(strict_types=1);


namespace Grifart\Tables;

/**
 * @template TableType of Table
 */
interface Modifications
{

	/**
	 * @internal
	 * @var array<string, mixed>
	 */
	public array $modifications { get; }

	/**
	 * null means that row is new (do INSERT)
	 *
	 * @internal
	 * @var PrimaryKey<TableType>|null
	 */
	public ?PrimaryKey $primaryKey { get; }

	/**
	 * With which table is this row associated
	 * @return class-string<TableType>
	 */
	public static function forTable(): string;

}
