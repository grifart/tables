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

	public function getDatabaseTypes(): array
	{
		return ['boolean'];
	}

	public function toDatabase(mixed $value): string
	{
		return $value ? 't' : 'f';
	}

	public function fromDatabase(mixed $value): bool
	{
		return $value === 't';
	}
}
