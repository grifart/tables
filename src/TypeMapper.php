<?php declare(strict_types=1);

namespace Grifart\Tables;

final class TypeMapper
{

	/**
	 * @var (callable(string $typeName, string $location): ?string)[]
	 */
	private $matchers = [];

	/**
	 * @var (callable(mixed $value, string $typeName): mixed)[]
	 */
	private $mappings = [];


	/**
	 * @param (callable(string $typeName, string $location): ?string) $typeMatcher
	 * @param (callable(mixed $value, string $typeName): mixed) $mapper
	 *
	 * TODO: isn't it too general? Or just map db-type name to php type and back?
	 */
	public function addMapping(callable $typeMatcher, callable $mapper) {
		$this->matchers[] = $typeMatcher;
		$this->mappings[] = $mapper;
	}

	public function map(string $location, string $typeName, $value) {
		if ($value === NULL) {
			return NULL; // todo: really do not translate?
		}

		foreach($this->matchers as $idx => $matcher) {
			$translatingType = $matcher($typeName, $location);
			if ($translatingType !== NULL) {
				$mapper = $this->mappings[$idx];
				return $mapper($value, $typeName);
			}
		}
		throw CouldNotMapTypeException::didYouRegisterTypeMapperFor($typeName, $value);
	}

	private function getTypeForValue($value): string {
		return !\is_object($value) ? \gettype($value) : \get_class($value);
	}

	public function mapType(string $location, string $typeName): string
	{
		foreach($this->matchers as $idx => $matcher) {
			if ( ($translatingType = $matcher($typeName, $location)) !== NULL) {
				return $translatingType;
			}
		}

		// todo runtime exception
		throw new \InvalidArgumentException("Cannot resolve type for '{$location}', database type '{$typeName}'.");
	}

}
