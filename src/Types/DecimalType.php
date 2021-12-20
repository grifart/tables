<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Brick\Math\BigDecimal;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<BigDecimal>
 */
final class DecimalType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve(BigDecimal::class);
	}

	public function toDatabase(mixed $value): string
	{
		return (string) $value;
	}

	public function fromDatabase(mixed $value): BigDecimal
	{
		return BigDecimal::of($value);
	}
}
