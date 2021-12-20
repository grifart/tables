<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;

/**
 * @template T
 */
interface Type
{
	public function getPhpType(): PhpType;

	/**
	 * @param T $value
	 */
	public function toDatabase(mixed $value): mixed;

	/**
	 * @return T
	 */
	public function fromDatabase(mixed $value): mixed;
}
