<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Connection;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\TypeResolver;
use Grifart\Tables\Types\DecimalType;

final class TypeResolverFactory
{
	public static function create(Connection $connection): TypeResolver
	{
		$resolver = new TypeResolver($connection);
		$resolver->addResolutionByLocation(new Identifier('public', 'test', 'whatever'), DecimalType::decimal());
		return $resolver;
	}
}
