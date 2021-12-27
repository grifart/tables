<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<UuidInterface>
 */
final class UuidType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve(UuidInterface::class);
	}

	public function toDatabase(mixed $value): string
	{
		return $value->toString();
	}

	public function fromDatabase(mixed $value): UuidInterface
	{
		return Uuid::fromString($value);
	}
}
