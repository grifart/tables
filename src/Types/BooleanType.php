<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
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

	public function toDatabase(mixed $value): mixed
	{
		if ($value === null) {
			return null;
		}

		return $value ? 't' : 'f';
	}

	public function fromDatabase(mixed $value): mixed
	{
		if ($value === null) {
			return null;
		}

		return $value === 't';
	}
}
