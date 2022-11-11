<?php

declare(strict_types=1);

namespace Grifart\Tables\Database;

use Dibi\Connection;
use Dibi\Expression;
use Dibi\Literal;

interface DatabaseType
{
	/**
	 * Database type name in minimal FQN form, as returned by the database.
	 *
	 * Correct:
	 *
	 * - text
	 * - my_schema.my_type
	 * - "mySchema".my_type
	 * - my_schema."myType"
	 *
	 *  Incorrect:
	 *
	 * - public.text
	 * - "my_schema".myType
	 */
	public function toSql(): Expression;
}
