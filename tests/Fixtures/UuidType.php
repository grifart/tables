<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\DatabaseType;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Database\NamedType;
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

	public function getDatabaseType(): DatabaseType
	{
		return new NamedType(new Identifier('uuid'));
	}

	public function toDatabase(mixed $value): Expression
	{
		return new Expression('%s', $value->get());
	}

	public function fromDatabase(mixed $value): mixed
	{
		return new Uuid($value);
	}
}
