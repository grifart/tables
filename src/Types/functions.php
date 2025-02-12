<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Deprecated;
use Grifart\Tables\Type;

/**
 * @template ValueType
 * @param ValueType $value
 * @param Type<ValueType> $type
 */
#[Deprecated('Use $type->toDatabase($value) instead')]
function mapToDatabase(mixed $value, Type $type): mixed
{
	return $type->toDatabase($value);
}

/**
 * @template ValueType
 * @param Type<ValueType> $type
 * @return ValueType|null
 */
#[Deprecated('Use $type->fromDatabase($value) instead')]
function mapFromDatabase(mixed $value, Type $type): mixed
{
	return $type->fromDatabase($value);
}
