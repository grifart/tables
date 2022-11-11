<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\DatabaseType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @template ValueType of \BackedEnum
 * @implements Type<ValueType>
 */
final class EnumType implements Type
{
	/**
	 * @param class-string<ValueType> $enumType
	 */
	private function __construct(
		private string $enumType,
		private DatabaseType $databaseType,
	) {}

	/**
	 * @template OfValueType of \BackedEnum
	 * @param class-string<OfValueType> $enumType
	 * @return self<OfValueType>
	 */
	public static function of(
		string $enumType,
		DatabaseType $databaseType,
	): self
	{
		return new self(
			$enumType,
			$databaseType,
		);
	}

	public function getPhpType(): PhpType
	{
		return resolve($this->enumType);
	}

	public function getDatabaseType(): DatabaseType
	{
		return $this->databaseType;
	}

	public function toDatabase(mixed $value): Expression
	{
		return new Expression(
			\is_string($value->value) ? '%s' : '%i',
			$value->value,
		);
	}

	public function fromDatabase(mixed $value): \BackedEnum
	{
		return $this->enumType::from($value);
	}
}
