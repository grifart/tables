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
 * @implements Type<mixed>
 */
final class JsonType implements Type
{
	private function __construct(
		private DatabaseType $databaseType,
	) {}

	public static function json(): self
	{
		return new self(BuiltInType::json());
	}

	public static function jsonb(): self
	{
		return new self(BuiltInType::jsonb());
	}

	public function getPhpType(): PhpType
	{
		return resolve('mixed');
	}

	public function getDatabaseType(): DatabaseType
	{
		return $this->databaseType;
	}

	public function toDatabase(mixed $value): Expression
	{
		return new Expression('%s', \json_encode($value, flags: \JSON_THROW_ON_ERROR));
	}

	public function fromDatabase(mixed $value): mixed
	{
		return \json_decode($value, flags: \JSON_THROW_ON_ERROR);
	}
}
