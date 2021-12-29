<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\TypeResolver;
use Grifart\Tables\Types\DecimalType;

final class TypeResolverFactory
{
	public static function create(): TypeResolver
	{
		$resolver = new TypeResolver();
		$resolver->addResolutionByLocation('public.test.whatever', new DecimalType());
		return $resolver;
	}
}
