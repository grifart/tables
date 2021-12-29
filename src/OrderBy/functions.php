<?php

declare(strict_types=1);

namespace Grifart\Tables\OrderBy;

use Grifart\Tables\Expression;

/**
 * @param Expression<mixed> $expression
 */
function asc(Expression $expression): OrderBy
{
	return new OrderBy($expression, OrderBy::ASC);
}

/**
 * @param Expression<mixed> $expression
 */
function desc(Expression $expression): OrderBy
{
	return new OrderBy($expression, OrderBy::DESC);
}
