<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Brick\DateTime\Instant;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\Parser\IsoParsers;
use Brick\DateTime\Parser\PatternParser;
use Brick\DateTime\Parser\PatternParserBuilder;
use Brick\DateTime\TimeZone;
use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\BuiltInType;
use Grifart\Tables\Database\DatabaseType;
use Grifart\Tables\Type;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

/**
 * @implements Type<Instant>
 */
final class InstantType implements Type
{
	private static PatternParser $parser;

	public function __construct()
	{
		self::$parser = (new PatternParserBuilder())
			->append(IsoParsers::localDate())
			->appendLiteral(' ')
			->append(IsoParsers::localTime())
			->toParser();
	}

	public function getPhpType(): PhpType
	{
		return resolve(Instant::class);
	}

	public function getDatabaseType(): DatabaseType
	{
		return BuiltInType::timestamp();
	}

	public function toDatabase(mixed $value): Expression
	{
		return new Expression('%s', (string) $value); // UTC
	}

	public function fromDatabase(mixed $value): Instant
	{
		$local = LocalDateTime::parse($value, self::$parser);
		return $local->atTimeZone(TimeZone::utc())->getInstant();
	}
}
