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
 * @implements PrimaryKey<ConfigTable>
 */
final class ConfigPrimaryKey implements PrimaryKey
{
	private function __construct(
		private Uuid $id,
	) {
	}


	public static function from(Uuid $id): self
	{
		return new self($id);
	}


	public static function fromRow(ConfigRow $row): self
	{
		return self::from($row->getId());
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
