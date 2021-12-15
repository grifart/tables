<?php declare(strict_types=1);


namespace Grifart\Tables;


interface Table
{

	public static function getSchema(): string;
	public static function getTableName(): string;
	public static function getPrimaryKeyClass(): string;
	public static function getRowClass(): string;
	public static function getModificationClass(): string;

	/** @return ColumnMetadata[] */
	public static function getDatabaseColumns(): array;
}
