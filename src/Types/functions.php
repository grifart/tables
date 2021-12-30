<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Grifart\Tables\Type;

/**
 * @template ValueType
 * @param ValueType $value
 * @param Type<ValueType> $type
 */
function mapToDatabase(mixed $value, Type $type): mixed
{
	return $value !== null
		? $type->toDatabase($value)
		: null;
}

/**
 * @template ValueType
 * @param Type<ValueType> $type
 * @return ValueType|null
 */
function mapFromDatabase(mixed $value, Type $type): mixed
{
	return $value !== null
		? $type->fromDatabase($value)
		: null;
}
