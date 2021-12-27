<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Brick\DateTime\Instant;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalTime;
use Brick\Math\BigDecimal;
use Grifart\Tables\Types\BinaryType;
use Grifart\Tables\Types\BooleanType;
use Grifart\Tables\Types\DateType;
use Grifart\Tables\Types\DecimalType;
use Grifart\Tables\Types\InstantType;
use Grifart\Tables\Types\IntType;
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

	public function __construct()
	{
		// default types
		$this->byTypeName['character'] = $this->byTypeName['character varying'] = $this->byTypeName['text'] = new TextType();
		$this->byTypeName['smallint'] = $this->byTypeName['integer'] = $this->byTypeName['bigint'] = new IntType();
		$this->byTypeName['boolean'] = new BooleanType();
		$this->byTypeName['bytea'] = new BinaryType();

		if (\class_exists(BigDecimal::class)) {
			$this->byTypeName['decimal'] = $this->byTypeName['numeric'] = new DecimalType();
		}

		if (\class_exists(Instant::class)) {
			$this->byTypeName['timestamp without time zone'] = new InstantType();
		}

		if (\class_exists(LocalTime::class)) {
			$this->byTypeName['time without time zone'] = new TimeType();
		}

		if (\class_exists(LocalDate::class)) {
			$this->byTypeName['date'] = new DateType();
		}

		if (\interface_exists(UuidInterface::class)) {
			$this->byTypeName['uuid'] = new UuidType();
		}
	}

	/**
	 * @param Type<mixed> $type
	 */
	public function addResolutionByTypeName(string $typeName, Type $type): void
	{
		if (\array_key_exists($typeName, $this->byTypeName)) {
			throw TypeAlreadyRegistered::forDatabaseType($typeName);
		}

		$this->byTypeName[$typeName] = $type;
	}

	/**
	 * @param array<string, Type<mixed>> $resolutions
	 */
	public function addResolutionsByName(array $resolutions): void
	{
		foreach ($resolutions as $typeName => $type) {
			$this->addResolutionByTypeName($typeName, $type);
		}
	}

	/**
	 * @param Type<mixed> $type
	 */
	public function addResolutionByLocation(string $location, Type $type): void
	{
		if (\array_key_exists($location, $this->byLocation)) {
			throw TypeAlreadyRegistered::forLocation($location);
		}

		$this->byLocation[$location] = $type;
	}

	/**
	 * @param array<string, Type<mixed>> $resolutions
	 */
	public function addResolutionsByLocation(array $resolutions): void
	{
		foreach ($resolutions as $location => $type) {
			$this->addResolutionByLocation($location, $type);
		}
	}

	/**
	 * @return Type<mixed>
	 */
	public function resolveType(
		string $typeName,
		string $location,
	): Type
	{
		return $this->byLocation[$location]
			?? $this->byTypeName[$typeName]
			?? throw UnresolvableType::of($location, $typeName);
	}
}
