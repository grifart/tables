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
 * @implements PrimaryKey<BulkTable>
 */
final class BulkPrimaryKey implements PrimaryKey
{
	private function __construct(
		private Uuid $id,
	) {
	}


	public static function from(Uuid $id): self
	{
		return new self($id);
	}


	public static function fromRow(BulkRow $row): self
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


	public function getId(): Uuid
	{
		return $this->id;
	}
}
