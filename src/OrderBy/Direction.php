<?php

declare(strict_types=1);

namespace Grifart\Tables\OrderBy;

use Dibi\Expression;

enum Direction
{
	case Ascending;
	case Descending;

	public function toSql(): Expression
	{
		return new Expression(
			'%sql',
			$this === self::Ascending ? 'ASC' : 'DESC',
		);
	}
}
