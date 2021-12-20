<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Connection;
use Grifart\Tables\TableManager;
use Grifart\Tables\TypeResolver;
use Grifart\Tables\Types\IntType;
use Grifart\Tables\Types\TextType;
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
		$typeResolver->addResolutionByTypeName('uuid', new UuidType());
		$typeResolver->addResolutionByLocation('public.test.score', new IntType());
		$typeResolver->addResolutionByTypeName('character varying', new TextType());
		return $typeResolver;
	}
}
