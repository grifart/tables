<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Expression as DibiExpression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\DatabaseType;

/**
 * @template ValueType
 */
interface Type
{
	public function getPhpType(): PhpType;

	public function getDatabaseType(): DatabaseType;

	/**
	 * @param ValueType $value
	 */
	public function toDatabase(mixed $value): DibiExpression;

	/**
	 * @return ValueType
	 */
	public function fromDatabase(mixed $value): mixed;
}
