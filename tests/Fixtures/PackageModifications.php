<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;
use Grifart\Tables\ModificationsTrait;

/**
 * @implements Modifications<PackagesTable>
 */
final class PackageModifications implements Modifications
{
	/** @use ModificationsTrait<PackagesTable> */
	use ModificationsTrait;

	public static function update(PackagePrimaryKey $primaryKey): self
	{
		return self::_update($primaryKey);
	}


	public static function new(): self
	{
		return self::_new();
	}


	public static function forTable(): string
	{
		return PackagesTable::class;
	}


	public function modifyName(string $name): void
	{
		$this->modifications['name'] = $name;
	}


	/**
	 * @param array{int, int, int} $version
	 */
	public function modifyVersion(array $version): void
	{
		$this->modifications['version'] = $version;
	}


	/**
	 * @param Version[] $previousVersions
	 */
	public function modifyPreviousVersions(array $previousVersions): void
	{
		$this->modifications['previousVersions'] = $previousVersions;
	}
}
