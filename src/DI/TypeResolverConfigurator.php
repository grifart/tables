<?php

declare(strict_types=1);

namespace Grifart\Tables\DI;

use Grifart\Tables\TypeResolver;

interface TypeResolverConfigurator
{
	public function configure(TypeResolver $resolver): void;
}
