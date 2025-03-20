<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Conditions\Composite;
use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\PrimaryKey;
use Grifart\Tables\Table;
use function Grifart\Tables\Conditions\equalTo;

/**
 * @implements PrimaryKey<GeneratedTable>
 */
final class GeneratedPrimaryKey implements PrimaryKey
{
	private function __construct(
		private int $id,
	) {
	}


	public static function from(int $id): self
	{
		return new self($id);
	}


	public static function fromRow(GeneratedRow $row): self
	{
		return self::from($row->getId());
	}


	/**
	 * @return string[]
	 */
	public static function getColumnNames(): array
	{
		return ['id'];
	}


	public function getCondition(Table $table): Condition
	{
		return Composite::and(
			$table->id()->is(equalTo($this->id)),
		);
	}


	public function getId(): int
	{
		return $this->id;
	}
}
