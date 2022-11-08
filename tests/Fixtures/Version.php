<?php declare(strict_types = 1);

namespace Grifart\Tables\Tests\Fixtures;

final class Version
{
	public function __construct(
		public int $major,
		public int $minor,
		public int $patch,
	) {}

	/** @return array{int,int,int} */
	public function toArray(): array
	{
		return [
			$this->major,
			$this->minor,
			$this->patch,
		];
	}
}
