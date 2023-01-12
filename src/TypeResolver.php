<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Brick\DateTime\Instant;
use Brick\Math\BigDecimal;
use Dibi\Connection;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Types\BinaryType;
use Grifart\Tables\Types\BooleanType;
use Grifart\Tables\Types\DateType;
use Grifart\Tables\Types\DecimalType;
use Grifart\Tables\Types\FloatType;
use Grifart\Tables\Types\InstantType;
use Grifart\Tables\Types\IntType;
use Grifart\Tables\Types\JsonType;
use Grifart\Tables\Types\TextType;
use Grifart\Tables\Types\TimeType;
use Grifart\Tables\Types\UuidType;
use Ramsey\Uuid\UuidInterface;

final class TypeResolver
{
	/** @var array<string, Type<mixed>> */
	private array $byTypeName = [];

	/** @var array<string, Type<mixed>> */
	private array $byLocation = [];

	public function __construct(
		private Connection $connection,
	)
	{
		// default types
		$this->addResolutionByTypeName(TextType::char());
		$this->addResolutionByTypeName(TextType::varchar());
		$this->addResolutionByTypeName(TextType::text());
		$this->addResolutionByTypeName(IntType::smallint());
		$this->addResolutionByTypeName(IntType::integer());
		$this->addResolutionByTypeName(IntType::bigint());
		$this->addResolutionByTypeName(FloatType::real());
		$this->addResolutionByTypeName(FloatType::double());
		$this->addResolutionByTypeName(new BooleanType());
		$this->addResolutionByTypeName(new BinaryType());
		$this->addResolutionByTypeName(JsonType::json());
		$this->addResolutionByTypeName(JsonType::jsonb());

		if (\class_exists(BigDecimal::class)) {
			$this->addResolutionByTypeName(DecimalType::decimal());
			$this->addResolutionByTypeName(DecimalType::numeric());
		}

		if (\class_exists(Instant::class)) {
			$this->addResolutionByTypeName(new InstantType());
			$this->addResolutionByTypeName(new TimeType());
			$this->addResolutionByTypeName(new DateType());
		}

		if (\interface_exists(UuidInterface::class)) {
			$this->addResolutionByTypeName(new UuidType());
		}
	}

	/**
	 * @template ValueType
	 * @param Type<ValueType> $type
	 */
	public function addResolutionByTypeName(Type $type): void
	{
		$databaseType = $type->getDatabaseType();
		$typeName = $this->connection->translate($databaseType->toSql());

		if (\array_key_exists($typeName, $this->byTypeName)) {
			throw TypeAlreadyRegistered::forDatabaseType($typeName);
		}

		$this->byTypeName[$typeName] = $type;
	}

	/**
	 * @template ValueType
	 * @param Type<ValueType> $type
	 */
	public function addResolutionByLocation(Identifier $location, Type $type): void
	{
		$locationString = $this->connection->translate($location->toSql());

		if (\array_key_exists($locationString, $this->byLocation)) {
			throw TypeAlreadyRegistered::forLocation($locationString);
		}

		$this->byLocation[$locationString] = $type;
	}

	/**
	 * @return Type<mixed>
	 */
	public function resolveType(
		string $typeName,
		Identifier $location,
	): Type
	{
		$locationString = $this->connection->translate($location->toSql());

		return $this->byLocation[$locationString]
			?? $this->byTypeName[$typeName]
			?? throw UnresolvableType::of($locationString, $typeName);
	}
}
