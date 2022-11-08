<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final class PackageRow implements Row
{
	/**
	 * @param array{int, int, int} $version
	 * @param Version[] $previousVersions
	 */
	private function __construct(
		private string $name,
		private array $version,
		private array $previousVersions,
	) {
	}


	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * @return array{int, int, int}
	 */
	public function getVersion(): array
	{
		return $this->version;
	}


	/**
	 * @return Version[]
	 */
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
