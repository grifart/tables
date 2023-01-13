<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Brick\DateTime\LocalTime;
use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\BuiltInType;
use Grifart\Tables\Database\DatabaseType;
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

	public function getDatabaseType(): DatabaseType
	{
		return BuiltInType::time();
	}

	public function toDatabase(mixed $value): Expression
	{
		return new Expression('%s', (string) $value);
	}

	public function fromDatabase(mixed $value): LocalTime
	{
		return LocalTime::parse($value);
	}
}
