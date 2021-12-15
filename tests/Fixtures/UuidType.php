<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

final class UuidType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve(Uuid::class);
	}

	public function toDatabase(mixed $value): string
	{
		return $value?->get();
	}

	public function fromDatabase(mixed $value): mixed
	{
		return $value !== null ? new Uuid($value) : null;
	}
}
