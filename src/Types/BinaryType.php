<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<string>
 */
final class BinaryType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve('string');
	}

	public function getDatabaseTypes(): array
	{
		return ['bytea'];
	}

	public function toDatabase(mixed $value): string
	{
		$unpacked = \unpack('H*', $value);
		\assert(\is_array($unpacked));

		return \sprintf('\x%s', \implode('', $unpacked));
	}

	public function fromDatabase(mixed $value): string
	{
		$result = \preg_match('/^\\\\x([0-9a-f]+)$/i', $value, $matches);
		\assert($result !== false);

		$result = \pack('H*', $matches[1]);
		\assert(\is_string($result));

		return $result;
	}
}
