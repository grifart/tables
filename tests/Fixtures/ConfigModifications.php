<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;
use Grifart\Tables\ModificationsTrait;

/**
 * @implements Modifications<ConfigTable>
 */
final class ConfigModifications implements Modifications
{
	/** @use ModificationsTrait<ConfigTable> */
	use ModificationsTrait;

	public static function update(ConfigPrimaryKey $primaryKey): self
	{
		return self::_update($primaryKey);
	}


	public static function new(): self
	{
		return self::_new();
	}


	public static function forTable(): string
	{
		return ConfigTable::class;
	}


	public function modifyId(Uuid $id): void
	{
		$this->modifications['id'] = $id;
	}


	public function modifyKey(string $key): void
	{
		$this->modifications['key'] = $key;
	}


	public function modifyValue(string $value): void
	{
		$this->modifications['value'] = $value;
	}
}
