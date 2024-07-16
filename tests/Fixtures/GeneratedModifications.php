<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;
use Grifart\Tables\ModificationsTrait;

/**
 * @implements Modifications<GeneratedTable>
 */
final class GeneratedModifications implements Modifications
{
	/** @use ModificationsTrait<GeneratedTable> */
	use ModificationsTrait;

	public static function update(GeneratedPrimaryKey $primaryKey): self
	{
		return self::_update($primaryKey);
	}


	public static function new(): self
	{
		return self::_new();
	}


	public static function forTable(): string
	{
		return GeneratedTable::class;
	}


	public function modifyDirect(int $direct): void
	{
		$this->modifications['direct'] = $direct;
	}
}
