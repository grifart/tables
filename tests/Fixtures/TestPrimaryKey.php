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
 * @implements PrimaryKey<TestsTable>
 */
final readonly class TestPrimaryKey implements PrimaryKey
{
	private function __construct(
		public Uuid $id,
	) {
	}


	public static function from(Uuid $id): self
	{
		return new self($id);
	}


	public static function fromRow(TestRow $row): self
	{
		return self::from($row->id);
	}


	/**
	 * @return string[]
	 */
	#[\Override]
	public static function getColumnNames(): array
	{
		return ['id'];
	}


	#[\Override]
	public function getCondition(Table $table): Condition
	{
		return Composite::and(
			$table->id->is(equalTo($this->id)),
		);
	}


	#[\Deprecated('Use $id property instead.')]
	public function getId(): Uuid
	{
		return $this->id;
	}
}
