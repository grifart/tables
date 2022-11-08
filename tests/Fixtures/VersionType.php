<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Literal;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\NamedIdentifier;
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
			new NamedIdentifier('public', 'packageVersion'),
			new IntType(),
			new IntType(),
			new IntType(),
		);
	}

	public function getPhpType(): PhpType
	{
		return resolve(Version::class);
	}

	public function toDatabase(mixed $value): Literal
	{
		return $this->tupleToDatabase($value->toArray());
	}

	public function fromDatabase(mixed $value): Version
	{
		[$major, $minor, $patch] = $this->tupleFromDatabase($value);
		return new Version($major, $minor, $patch);
	}
}
