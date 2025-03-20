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
 * @implements PrimaryKey<PackagesTable>
 */
final class PackagePrimaryKey implements PrimaryKey
{
	private function __construct(
		private string $name,
	) {
	}


	public static function from(string $name): self
	{
		return new self($name);
	}


	public static function fromRow(PackageRow $row): self
	{
		return self::from($row->getName());
	}


	/**
	 * @return string[]
	 */
	public static function getColumnNames(): array
	{
		return ['name'];
	}


	public function getCondition(Table $table): Condition
	{
		return Composite::and(
			$table->name()->is(equalTo($this->name)),
		);
	}


	public function getName(): string
	{
		return $this->name;
	}
}
