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
 * @implements Type<bool>
 */
final class BooleanType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve('bool');
	}

	public function getDatabaseType(): DatabaseType
	{
		return BuiltInType::boolean();
	}

	public function toDatabase(mixed $value): Expression
	{
		return new Expression('%b', $value);
	}

	public function fromDatabase(mixed $value): bool
	{
		return $value === 't';
	}
}
