<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;

interface Type
{
	public function getPhpType(): PhpType;

	public function toDatabase(mixed $value): mixed;

	public function fromDatabase(mixed $value): mixed;
}
