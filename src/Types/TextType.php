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
 * @implements Type<string>
 */
final class TextType implements Type
{
	private function __construct(
		private DatabaseType $databaseType,
	) {}

	public static function char(): self
	{
		return new self(BuiltInType::char());
	}

	public static function varchar(): self
	{
		return new self(BuiltInType::varchar());
	}

	public static function text(): self
	{
		return new self(BuiltInType::text());
	}

	public function getPhpType(): PhpType
	{
		return resolve('string');
	}

	public function getDatabaseType(): DatabaseType
	{
		return $this->databaseType;
	}

	public function toDatabase(mixed $value): Expression
	{
		return new Expression('%s', $value);
	}

	public function fromDatabase(mixed $value): string
	{
		return $value;
	}
}
