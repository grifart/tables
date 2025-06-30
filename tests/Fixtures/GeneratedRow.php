<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final readonly class GeneratedRow implements Row
{
	private function __construct(
		public int $id,
		public int $double,
		public int $direct,
	) {
	}


	#[\Deprecated('Use $id property instead.')]
	public function getId(): int
	{
		return $this->id;
	}


	#[\Deprecated('Use $double property instead.')]
	public function getDouble(): int
	{
		return $this->double;
	}


	#[\Deprecated('Use $direct property instead.')]
	public function getDirect(): int
	{
		return $this->direct;
	}


	#[\Override]
	public static function reconstitute(array $values): static
	{
		/** @var array{id: int, double: int, direct: int} $values */
		return new static($values['id'], $values['double'], $values['direct']);
	}
}
