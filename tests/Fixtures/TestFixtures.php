<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Connection;
use Grifart\Tables\Database\Identifier;
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

	public static function createTypeResolver(Connection $connection): TypeResolver
	{
		$typeResolver = new TypeResolver($connection);
		$typeResolver->addResolutionByLocation(new Identifier('public', 'test', 'id'), new UuidType());
		$typeResolver->addResolutionByLocation(new Identifier('public', 'test', 'score'), IntType::integer());
		$typeResolver->addResolutionByLocation(new Identifier('public', 'config', 'id'), new UuidType());
		$typeResolver->addResolutionByLocation(new Identifier('public', 'package', 'version'), new TupleVersionType());
		$typeResolver->addResolutionByLocation(new Identifier('public', 'package', 'previousVersions'), ArrayType::of(new VersionType()));
		return $typeResolver;
	}
}
