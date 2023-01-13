<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Database\NamedType;
use Grifart\Tables\Types\CompositeType;
use Grifart\Tables\Types\IntType;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @extends CompositeType<Version>
 */
final class VersionType extends CompositeType
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
		return resolve(Version::class);
	}

	public function toDatabase(mixed $value): Expression
	{
		return $this->tupleToDatabase($value->toArray());
	}

	public function fromDatabase(mixed $value): Version
	{
		[$major, $minor, $patch] = $this->tupleFromDatabase($value);
		return new Version($major, $minor, $patch);
	}
}
