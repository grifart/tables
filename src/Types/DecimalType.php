<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Brick\Math\BigDecimal;
use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\BuiltInType;
use Grifart\Tables\Database\DatabaseType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<BigDecimal>
 */
final class DecimalType implements Type
{
	private function __construct(
		private DatabaseType $databaseType,
	)
	{
	}

	public static function decimal(): self
	{
		return new self(BuiltInType::decimal());
	}

	public static function numeric(): self
	{
		return new self(BuiltInType::numeric());
	}

	public static function real(): self
	{
		return new self(BuiltInType::real());
	}

	public function getPhpType(): PhpType
	{
		return resolve(BigDecimal::class);
	}

	public function getDatabaseType(): DatabaseType
	{
		return $this->databaseType;
	}

	public function toDatabase(mixed $value): Expression
	{
		return new Expression('%s', (string) $value);
	}

	public function fromDatabase(mixed $value): BigDecimal
	{
		return BigDecimal::of($value);
	}
}
