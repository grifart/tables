<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Brick\DateTime\LocalTime;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<LocalTime>
 */
final class TimeType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve(LocalTime::class);
	}

	public function toDatabase(mixed $value): mixed
	{
		return $value !== null
			? (string) $value
			: null;
	}

	public function fromDatabase(mixed $value): mixed
	{
		return $value !== null
			?  LocalTime::parse($value)
			: null;
	}
}
