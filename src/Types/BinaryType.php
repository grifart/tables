<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\BuiltInType;
use Grifart\Tables\Database\DatabaseType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<string>
 */
final class BinaryType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve('string');
	}

	public function getDatabaseType(): DatabaseType
	{
		return BuiltInType::bytea();
	}

	public function toDatabase(mixed $value): Expression
	{
		return new Expression('%bin', $value);
	}

	public function fromDatabase(mixed $value): string
	{
		return \pg_unescape_bytea($value);
	}
}
