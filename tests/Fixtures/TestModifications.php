<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Modifications;
use Grifart\Tables\ModificationsTrait;

/**
 * @implements Modifications<TestsTable>
 */
final class TestModifications implements Modifications
{
	/** @use ModificationsTrait<TestsTable> */
	use ModificationsTrait;

	public static function update(TestPrimaryKey $primaryKey): self
	{
		return self::_update($primaryKey);
	}


	public static function new(): self
	{
		return self::_new();
	}


	public static function forTable(): string
	{
		return TestsTable::class;
	}


	public function modifyId(Uuid $id): void
	{
		$this->modifications['id'] = $id;
	}


	public function modifyScore(int $score): void
	{
		$this->modifications['score'] = $score;
	}


	public function modifyDetails(?string $details): void
	{
		$this->modifications['details'] = $details;
	}
}
