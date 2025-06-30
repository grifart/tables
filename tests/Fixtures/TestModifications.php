<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;

/**
 * @implements Modifications<TestsTable>
 */
final class TestModifications implements Modifications
{
	/** @var array<string, mixed> */
	public private(set) array $modifications = [];

	public Uuid $id {
		set {
			$this->modifications['id'] = $value;
		}
	}

	public int $score {
		set {
			$this->modifications['score'] = $value;
		}
	}

	public ?string $details {
		set {
			$this->modifications['details'] = $value;
		}
	}


	private function __construct(
		public readonly ?TestPrimaryKey $primaryKey = null,
	) {
	}


	public static function update(TestPrimaryKey $primaryKey): self
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
		return TestsTable::class;
	}


	#[\Deprecated('Use $id property instead.')]
	public function modifyId(Uuid $id): void
	{
		$this->modifications['id'] = $id;
	}


	#[\Deprecated('Use $score property instead.')]
	public function modifyScore(int $score): void
	{
		$this->modifications['score'] = $score;
	}


	#[\Deprecated('Use $details property instead.')]
	public function modifyDetails(?string $details): void
	{
		$this->modifications['details'] = $details;
	}
}
