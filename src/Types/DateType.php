<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Brick\DateTime\LocalDate;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<LocalDate>
 */
final class DateType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve(LocalDate::class);
	}

	public function toDatabase(mixed $value): string
	{
		return (string) $value;
	}

	public function fromDatabase(mixed $value): LocalDate
	{
		return LocalDate::parse($value);
	}
}
