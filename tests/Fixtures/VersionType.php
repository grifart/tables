<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\NamedIdentifier;
use Grifart\Tables\Types\CompositeType;
use Grifart\Tables\Types\IntType;
use function Grifart\ClassScaffolder\Definition\Types\tuple;

/**
 * @extends CompositeType<array{int, int, int}>
 */
final class VersionType extends CompositeType
{
	public function __construct()
	{
		parent::__construct(
			new NamedIdentifier('version'),
			new IntType(),
			new IntType(),
			new IntType(),
		);
	}

	public function getPhpType(): PhpType
	{
		return tuple('int', 'int', 'int');
	}

	public function toDatabase(mixed $value): mixed
	{
		return $this->tupleToDatabase($value);
	}

	public function fromDatabase(mixed $value): mixed
	{
		/** @var array{int, int, int} $version */
		$version = $this->tupleFromDatabase($value);
		return $version;
	}
}
