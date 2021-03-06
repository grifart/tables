<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<string>
 */
final class TextType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve('string');
	}

	public function getDatabaseTypes(): array
	{
		return ['character', 'character varying', 'text'];
	}

	public function toDatabase(mixed $value): string
	{
		return $value;
	}

	public function fromDatabase(mixed $value): string
	{
		return $value;
	}
}
