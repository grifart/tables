<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Brick\DateTime\Instant;
use Brick\Math\BigDecimal;
use Grifart\Tables\Types\BinaryType;
use Grifart\Tables\Types\BooleanType;
use Grifart\Tables\Types\DateType;
use Grifart\Tables\Types\DecimalType;
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

	public function __construct()
	{
		// default types
		$this->addType(new TextType());
		$this->addType(new IntType());
		$this->addType(new BooleanType());
		$this->addType(new BinaryType());
		$this->addType(new JsonType());

		if (\class_exists(BigDecimal::class)) {
			$this->addType(new DecimalType());
		}

		if (\class_exists(Instant::class)) {
			$this->addType(new InstantType());
			$this->addType(new TimeType());
			$this->addType(new DateType());
		}

		if (\interface_exists(UuidInterface::class)) {
			$this->addType(new UuidType());
		}
	}

	/**
	 * @param Type<mixed> $type
	 */
	public function addType(Type $type): void
	{
		$databaseTypes = $type->getDatabaseTypes();
		if (\count($databaseTypes) === 0) {
			throw MissingDatabaseTypeResolution::of($type);
		}

		foreach ($databaseTypes as $databaseType) {
			$this->addResolutionByTypeName($databaseType, $type);
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
