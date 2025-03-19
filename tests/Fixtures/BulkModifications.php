<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;
use Grifart\Tables\ModificationsTrait;

/**
 * @implements Modifications<BulkTable>
 */
final class BulkModifications implements Modifications
{
	/** @use ModificationsTrait<BulkTable> */
	use ModificationsTrait;

	public static function update(BulkPrimaryKey $primaryKey): self
	{
		return self::_update($primaryKey);
	}


	public static function new(): self
	{
		return self::_new();
	}


	public static function forTable(): string
	{
		return BulkTable::class;
	}


	public function modifyId(Uuid $id): void
	{
		$this->modifications['id'] = $id;
	}


	public function modifyValue(int $value): void
	{
		$this->modifications['value'] = $value;
	}


	public function modifyFlagged(bool $flagged): void
	{
		$this->modifications['flagged'] = $flagged;
	}
}
