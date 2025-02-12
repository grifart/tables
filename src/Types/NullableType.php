<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Dibi\Expression;
use Dibi\Expression as DibiExpression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\DatabaseType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\nullable;

/**
 * @template ValueType
 * @implements Type<ValueType|null>
 */
final readonly class NullableType implements Type
{
	/**
	 * @param Type<ValueType> $type
	 */
	private function __construct(private Type $type) {}

	/**
	 * @template OfValueType
	 * @param Type<OfValueType> $type
	 * @return self<OfValueType>
	 */
	public static function of(Type $type): self
	{
		return new self($type);
	}

	public function getPhpType(): PhpType
	{
		return nullable($this->type->getPhpType());
	}

	public function getDatabaseType(): DatabaseType
	{
		return $this->type->getDatabaseType();
	}

	public function toDatabase(mixed $value): DibiExpression
	{
		if ($value === null) {
			return new Expression('%sql', 'NULL');
		}

		return $this->type->toDatabase($value);
	}

	public function fromDatabase(mixed $value): mixed
	{
		if ($value === null) {
			return null;
		}

		return $this->type->fromDatabase($value);
	}
}
