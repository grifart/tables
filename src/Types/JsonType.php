<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<mixed>
 */
final class JsonType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve('mixed');
	}

	public function getDatabaseTypes(): array
	{
		return ['json', 'jsonb'];
	}

	public function toDatabase(mixed $value): mixed
	{
		return \json_encode($value, \JSON_THROW_ON_ERROR);
	}

	public function fromDatabase(mixed $value): mixed
	{
		return \json_decode($value, flags: \JSON_THROW_ON_ERROR);
	}
}
