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

	public function getDatabaseTypes(): array
	{
		return ['time without time zone'];
	}

	public function toDatabase(mixed $value): string
	{
		return (string) $value;
	}

	public function fromDatabase(mixed $value): LocalTime
	{
		return LocalTime::parse($value);
	}
}
