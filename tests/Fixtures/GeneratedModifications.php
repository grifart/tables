<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;

/**
 * @implements Modifications<GeneratedTable>
 */
final class GeneratedModifications implements Modifications
{
	/** @var array<string, mixed> */
	public private(set) array $modifications = [];

	public int $direct {
		set {
			$this->modifications['direct'] = $value;
		}
	}


	private function __construct(
		public readonly ?GeneratedPrimaryKey $primaryKey = null,
	) {
	}


	public static function update(GeneratedPrimaryKey $primaryKey): self
	{
		return new self($primaryKey);
	}


	public static function new(): self
	{
		return new self();
	}


	public static function forTable(): string
	{
		return GeneratedTable::class;
	}


	#[\Deprecated('Use $direct property instead.')]
	public function modifyDirect(int $direct): void
	{
		$this->modifications['direct'] = $direct;
	}
}
