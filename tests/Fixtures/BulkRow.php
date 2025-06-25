<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final readonly class BulkRow implements Row
{
	private function __construct(
		public Uuid $id,
		public int $value,
		public bool $flagged,
	) {
	}


	#[\Deprecated('Use $id property instead.')]
	public function getId(): Uuid
	{
		return $this->id;
	}


	#[\Deprecated('Use $value property instead.')]
	public function getValue(): int
	{
		return $this->value;
	}


	#[\Deprecated('Use $flagged property instead.')]
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
