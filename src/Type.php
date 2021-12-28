<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;

/**
 * @template ValueType
 */
interface Type
{
	public function getPhpType(): PhpType;

	/**
	 * @return string[]
	 */
	public function getDatabaseTypes(): array;

	/**
	 * @param ValueType $value
	 */
	public function toDatabase(mixed $value): mixed;

	/**
	 * @return ValueType
	 */
	public function fromDatabase(mixed $value): mixed;
}
