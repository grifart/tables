<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\PrimaryKey;
use Grifart\Tables\Table;

final class TestPrimaryKey implements PrimaryKey
{
	private function __construct(private Uuid $id)
	{
	}


	public static function from(Uuid $id): self
	{
		return new self($id);
	}


	public static function fromRow(TestRow $row): self
	{
		return self::from($row->getId());
	}


	public function getQuery(Table $table): array
	{
		$query = [];
		$query[$table::ID] = $table->id()->map($this->id);
		return $query;
	}


	public function getId(): Uuid
	{
		return $this->id;
	}
}
