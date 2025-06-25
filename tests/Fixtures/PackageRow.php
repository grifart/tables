<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final readonly class PackageRow implements Row
{
	/**
	 * @param array{int, int, int} $version
	 * @param Version[] $previousVersions
	 */
	private function __construct(
		public string $name,
		public array $version,
		public array $previousVersions,
	) {
	}


	#[\Deprecated('Use $name property instead.')]
	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * @return array{int, int, int}
	 */
	#[\Deprecated('Use $version property instead.')]
	public function getVersion(): array
	{
		return $this->version;
	}


	/**
	 * @return Version[]
	 */
	#[\Deprecated('Use $previousVersions property instead.')]
	public function getPreviousVersions(): array
	{
		return $this->previousVersions;
	}


	public static function reconstitute(array $values): static
	{
		/** @var array{name: string, version: array{int, int, int}, previousVersions: Version[]} $values */
		return new static($values['name'], $values['version'], $values['previousVersions']);
	}
}
