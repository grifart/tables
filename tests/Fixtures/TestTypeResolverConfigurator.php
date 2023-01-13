<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Database\Identifier;
use Grifart\Tables\DI\TypeResolverConfigurator;
use Grifart\Tables\TypeResolver;
use Grifart\Tables\Types\DecimalType;

final class TestTypeResolverConfigurator implements TypeResolverConfigurator
{
	public function configure(TypeResolver $resolver): void
	{
		$resolver->addResolutionByLocation(new Identifier('public', 'test', 'whatever2'), DecimalType::decimal());
	}
}
