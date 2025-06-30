<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;

/**
 * @implements Modifications<PackagesTable>
 */
final class PackageModifications implements Modifications
{
	/** @var array<string, mixed> */
	public private(set) array $modifications = [];

	public string $name {
		set {
			$this->modifications['name'] = $value;
		}
	}

	/** @var array{int, int, int} */
	public array $version {
		set {
			$this->modifications['version'] = $value;
		}
	}

	/** @var Version[] */
	public array $previousVersions {
		set {
			$this->modifications['previousVersions'] = $value;
		}
	}


	private function __construct(
		public readonly ?PackagePrimaryKey $primaryKey = null,
	) {
	}


	public static function update(PackagePrimaryKey $primaryKey): self
	{
		return new self($primaryKey);
	}


	public static function new(): self
	{
		return new self();
	}


	public static function forTable(): string
	{
		return PackagesTable::class;
	}


	#[\Deprecated('Use $name property instead.')]
	public function modifyName(string $name): void
	{
		$this->modifications['name'] = $name;
	}


	/**
	 * @param array{int, int, int} $version
	 */
	#[\Deprecated('Use $version property instead.')]
	public function modifyVersion(array $version): void
	{
		$this->modifications['version'] = $version;
	}


	/**
	 * @param Version[] $previousVersions
	 */
	#[\Deprecated('Use $previousVersions property instead.')]
	public function modifyPreviousVersions(array $previousVersions): void
	{
		$this->modifications['previousVersions'] = $previousVersions;
	}
}
