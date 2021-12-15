<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<int>
 */
final class IntType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve('int');
	}

	public function toDatabase(mixed $value): mixed
	{
		return $value !== null ? (int) $value : null;
	}

	public function fromDatabase(mixed $value): mixed
	{
		return $value !== null ? (int) $value : null;
	}
}
