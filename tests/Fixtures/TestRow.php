<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final class TestRow implements Row
{
	private function __construct(
		private Uuid $id,
		private int $score,
		private ?string $details,
	) {
	}


	public function getId(): Uuid
	{
		return $this->id;
	}


	public function getScore(): int
	{
		return $this->score;
	}


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
