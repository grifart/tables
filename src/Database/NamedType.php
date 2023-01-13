<?php

declare(strict_types=1);

namespace Grifart\Tables\Database;

use Dibi\Expression;

final class NamedType implements DatabaseType
{
	public function __construct(
		private Identifier $identifier,
	)
	{
	}

	public function toSql(): Expression
	{
		return $this->identifier->toSql();
	}
}
