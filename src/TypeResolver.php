<?php

declare(strict_types=1);

namespace Grifart\Tables;

final class TypeResolver
{
	/** @var array<string, Type<mixed>> */
	private array $byTypeName = [];

	/** @var array<string, Type<mixed>> */
	private array $byLocation = [];

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
