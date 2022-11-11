<?php

declare(strict_types=1);

namespace Grifart\Tables\OrderBy;

use Dibi\Expression;

enum Nulls
{
	case First;
	case Last;

	public function toSql(): Expression
	{
		return new Expression(
			'NULLS %sql',
			$this === self::First ? 'FIRST' : 'LAST',
		);
	}

	public static function default(Direction $direction): self
	{
		return match ($direction) {
			Direction::Ascending => Nulls::Last,
			Direction::Descending => Nulls::First,
		};
	}
}
