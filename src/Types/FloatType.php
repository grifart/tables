<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\BuiltInType;
use Grifart\Tables\Database\DatabaseType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;
use function is_infinite;
use function is_nan;
use function sprintf;
use const INF;
use const NAN;

/**
 * @implements Type<float>
 */
final class FloatType implements Type
{
	private function __construct(
		private DatabaseType $databaseType,
	) {}

	public static function real(): self
	{
		return new self(BuiltInType::real());
	}

	public static function double(): self
	{
		return new self(BuiltInType::double());
	}

	public function getPhpType(): PhpType
	{
		return resolve('float');
	}

	public function getDatabaseType(): DatabaseType
	{
		return $this->databaseType;
	}

	public function toDatabase(mixed $value): Expression
	{
		if (is_nan($value)) {
			return new Expression('%s', 'NaN');
		}

		if (is_infinite($value)) {
			return new Expression('%s', sprintf('%sInfinity', $value < 0 ? '-' : ''));
		}

		return new Expression('%f', $value);
	}

	public function fromDatabase(mixed $value): float
	{
		return match ($value) {
			'Infinity' => INF,
			'-Infinity' => -INF,
			'NaN' => NAN,
			default => (float) $value,
		};
	}
}
