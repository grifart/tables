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
	 * @param T|null $value
	 */
	public function toDatabase(mixed $value): mixed;

	/**
	 * @return T|null
	 */
	public function fromDatabase(mixed $value): mixed;
}
