<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final class GeneratedRow implements Row
{
	private function __construct(
		private int $id,
		private int $double,
		private int $direct,
	) {
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function getDouble(): int
	{
		return $this->double;
	}


	public function getDirect(): int
	{
		return $this->direct;
	}


	public static function reconstitute(array $values): static
	{
		/** @var array{id: int, double: int, direct: int} $values */
		return new static($values['id'], $values['double'], $values['direct']);
	}
}
