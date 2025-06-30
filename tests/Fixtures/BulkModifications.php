<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;

/**
 * @implements Modifications<BulkTable>
 */
final class BulkModifications implements Modifications
{
	/** @var array<string, mixed> */
	public private(set) array $modifications = [];

	public Uuid $id {
		set {
			$this->modifications['id'] = $value;
		}
	}

	public int $value {
		set {
			$this->modifications['value'] = $value;
		}
	}

	public bool $flagged {
		set {
			$this->modifications['flagged'] = $value;
		}
	}


	private function __construct(
		public readonly ?BulkPrimaryKey $primaryKey = null,
	) {
	}


	public static function update(BulkPrimaryKey $primaryKey): self
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
		return BulkTable::class;
	}


	#[\Deprecated('Use $id property instead.')]
	public function modifyId(Uuid $id): void
	{
		$this->modifications['id'] = $id;
	}


	#[\Deprecated('Use $value property instead.')]
	public function modifyValue(int $value): void
	{
		$this->modifications['value'] = $value;
	}


	#[\Deprecated('Use $flagged property instead.')]
	public function modifyFlagged(bool $flagged): void
	{
		$this->modifications['flagged'] = $flagged;
	}
}
