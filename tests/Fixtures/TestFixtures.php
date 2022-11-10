<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Connection;
use Grifart\Tables\NamedIdentifier;
use Grifart\Tables\TableManager;
use Grifart\Tables\TypeResolver;
use Grifart\Tables\Types\ArrayType;
use Grifart\Tables\Types\IntType;
use Nette\StaticClass;

final class TestFixtures
{
	use StaticClass;

	public static function createTableManager(Connection $connection): TableManager
	{
		return new TableManager($connection);
	}

	public static function createTypeResolver(): TypeResolver
	{
		$typeResolver = new TypeResolver();
		$typeResolver->addResolutionByLocation('public.test.id', new UuidType());
		$typeResolver->addResolutionByLocation('public.test.score', new IntType());
		$typeResolver->addResolutionByLocation('public.package.version', new TupleVersionType());
		$typeResolver->addResolutionByLocation('public.package.previousVersions', ArrayType::of(new NamedIdentifier('public', 'packageVersion'),new VersionType()));
		return $typeResolver;
	}
}
