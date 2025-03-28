<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final class BulkRow implements Row
{
	private function __construct(
		private Uuid $id,
		private int $value,
		private bool $flagged,
	) {
	}


	public function getId(): Uuid
	{
		return $this->id;
	}


	public function getValue(): int
	{
		return $this->value;
	}


	public function getFlagged(): bool
	{
		return $this->flagged;
	}


	public static function reconstitute(array $values): static
	{
		/** @var array{id: Uuid, value: int, flagged: bool} $values */
		return new static($values['id'], $values['value'], $values['flagged']);
	}
}
