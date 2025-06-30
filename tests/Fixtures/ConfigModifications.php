<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;

/**
 * @implements Modifications<ConfigTable>
 */
final class ConfigModifications implements Modifications
{
	/** @var array<string, mixed> */
	public private(set) array $modifications = [];

	public Uuid $id {
		set {
			$this->modifications['id'] = $value;
		}
	}

	public string $key {
		set {
			$this->modifications['key'] = $value;
		}
	}

	public string $value {
		set {
			$this->modifications['value'] = $value;
		}
	}


	private function __construct(
		public readonly ?ConfigPrimaryKey $primaryKey = null,
	) {
	}


	public static function update(ConfigPrimaryKey $primaryKey): self
	{
		return new self($primaryKey);
	}


	public static function new(): self
	{
		return new self();
	}


	#[\Override]
	public static function forTable(): string
	{
		return ConfigTable::class;
	}


	#[\Deprecated('Use $id property instead.')]
	public function modifyId(Uuid $id): void
	{
		$this->modifications['id'] = $id;
	}


	#[\Deprecated('Use $key property instead.')]
	public function modifyKey(string $key): void
	{
		$this->modifications['key'] = $key;
	}


	#[\Deprecated('Use $value property instead.')]
	public function modifyValue(string $value): void
	{
		$this->modifications['value'] = $value;
	}
}
