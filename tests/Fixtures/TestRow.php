<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final readonly class TestRow implements Row
{
	private function __construct(
		public Uuid $id,
		public int $score,
		public ?string $details,
	) {
	}


	#[\Deprecated('Use $id property instead.')]
	public function getId(): Uuid
	{
		return $this->id;
	}


	#[\Deprecated('Use $score property instead.')]
	public function getScore(): int
	{
		return $this->score;
	}


	#[\Deprecated('Use $details property instead.')]
	public function getDetails(): ?string
	{
		return $this->details;
	}


	public static function reconstitute(array $values): static
	{
		/** @var array{id: Uuid, score: int, details: string|null} $values */
		return new static($values['id'], $values['score'], $values['details']);
	}
}
