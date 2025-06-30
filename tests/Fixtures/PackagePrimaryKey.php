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
final readonly class PackagePrimaryKey implements PrimaryKey
{
	private function __construct(
		public string $name,
	) {
	}


	public static function from(string $name): self
	{
		return new self($name);
	}


	public static function fromRow(PackageRow $row): self
	{
		return self::from($row->name);
	}


	/**
	 * @return string[]
	 */
	#[\Override]
	public static function getColumnNames(): array
	{
		return ['name'];
	}


	#[\Override]
	public function getCondition(Table $table): Condition
	{
		return Composite::and(
			$table->name->is(equalTo($this->name)),
		);
	}


	#[\Deprecated('Use $name property instead.')]
	public function getName(): string
	{
		return $this->name;
	}
}
