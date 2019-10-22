<?php declare(strict_types=1);


namespace Grifart\Tables;


use function Grifart\AssertFunction\assertSignature;
use function Grifart\AssertFunction\nullable;

final class TypeMapper
{

	private $matchers;
	private $mappings;

	public function __construct()
	{
		$this->mappings = [];
		$this->matchers = [];
	}

	// TODO: isn't it too general? Or just map db-type name to php type and back?
	public function addMapping(callable $typeMatcher, callable $mapper) {

		assertSignature($typeMatcher, ['string', 'string'], nullable('string'));
		assertSignature($mapper, ['any'], 'mixed');

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
//				assertSignature(
//					$mapper, [
//						$this->getTypeForValue($value)
//					],
//					'mixed' // todo: $translatingType does not work for reverse mapping
//				);
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
