<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

final class Uuid {
	public function __construct(private string $uuid) {}
	public function get(): string
	{
		return $this->uuid;
	}
}
