<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<Uuid>
 */
final class UuidType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve(Uuid::class);
	}

	public function getDatabaseTypes(): array
	{
		return ['uuid'];
	}

	public function toDatabase(mixed $value): mixed
	{
		return $value->get();
	}

	public function fromDatabase(mixed $value): mixed
	{
		return new Uuid($value);
	}
}
