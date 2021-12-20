<?php declare(strict_types=1);


namespace Grifart\Tables;


interface Table
{

	public static function getSchema(): string;
	public static function getTableName(): string;

	/** @return class-string<PrimaryKey<static>> */
	public static function getPrimaryKeyClass(): string;

	/** @return class-string<Row> */
	public static function getRowClass(): string;

	/** @return class-string<Modifications<static>> */
	public static function getModificationClass(): string;

	/** @return ColumnMetadata[] */
	public static function getDatabaseColumns(): array;

	/**
	 * @return Type<mixed>
	 */
	public function getTypeOf(string $columnName): Type;
}
