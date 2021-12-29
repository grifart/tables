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

	public function getDatabaseTypes(): array
	{
		return ['smallint', 'integer', 'bigint'];
	}

	public function toDatabase(mixed $value): int
	{
		return (int) $value;
	}

	public function fromDatabase(mixed $value): int
	{
		return (int) $value;
	}
}
