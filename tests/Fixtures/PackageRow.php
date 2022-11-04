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
	 */
	private function __construct(
		private string $name,
		private array $version,
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


	public static function reconstitute(array $values): static
	{
		/** @var array{name: string, version: array{int, int, int}} $values */
		return new static($values['name'], $values['version']);
	}
}
