<?php

declare(strict_types=1);

namespace Grifart\Tables\Database;

use Dibi\Expression;
use Dibi\Literal;

final class ArrayType implements DatabaseType
{
	public function __construct(
		private DatabaseType $itemType,
	)
	{
	}

	public function toSql(): Expression
	{
		return new Expression(
			'?%sql',
			$this->itemType->toSql(),
			new Literal('[]'),
		);
	}
}
