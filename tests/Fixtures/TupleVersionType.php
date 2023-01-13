<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Database\NamedType;
use Grifart\Tables\Types\CompositeType;
use Grifart\Tables\Types\IntType;
use function Grifart\ClassScaffolder\Definition\Types\tuple;

/**
 * @extends CompositeType<array{int, int, int}>
 */
final class TupleVersionType extends CompositeType
{
	public function __construct()
	{
		parent::__construct(
			new NamedType(new Identifier('public', 'packageVersion')),
			IntType::integer(),
			IntType::integer(),
			IntType::integer(),
		);
	}

	public function getPhpType(): PhpType
	{
		return tuple('int', 'int', 'int');
	}

	public function toDatabase(mixed $value): Expression
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
