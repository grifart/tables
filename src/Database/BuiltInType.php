<?php

declare(strict_types=1);

namespace Grifart\Tables\Database;

use Dibi\Expression;
use Dibi\Literal;

final class BuiltInType implements DatabaseType
{
	private function __construct(
		private string $typeName,
	)
	{
	}

	public static function bytea(): self
	{
		return new self('bytea');
	}

	public static function boolean(): self
	{
		return new self('boolean');
	}

	public static function date(): self
	{
		return new self('date');
	}

	public static function decimal(): self
	{
		return new self('decimal');
	}

	public static function numeric(): self
	{
		return new self('numeric');
	}

	public static function real(): self
	{
		return new self('real');
	}

	public static function double(): self
	{
		return new self('double precision');
	}

	public static function timestamp(): self
	{
		return new self('timestamp without time zone');
	}

	public static function timestampTz(): self
	{
		return new self('timestamp with time zone');
	}

	public static function smallint(): self
	{
		return new self('smallint');
	}

	public static function integer(): self
	{
		return new self('integer');
	}

	public static function bigint(): self
	{
		return new self('bigint');
	}

	public static function json(): self
	{
		return new self('json');
	}

	public static function jsonb(): self
	{
		return new self('jsonb');
	}

	public static function char(): self
	{
		return new self('character');
	}

	public static function varchar(): self
	{
		return new self('character varying');
	}

	public static function text(): self
	{
		return new self('text');
	}

	public static function time(): self
	{
		return new self('time without time zone');
	}

	public static function timeTz(): self
	{
		return new self('time with time zone');
	}

	public static function uuid(): self
	{
		return new self('uuid');
	}

	public function toSql(): Expression
	{
		return new Expression('?', new Literal($this->typeName));
	}
}
