<?php declare(strict_types=1);

namespace Grifart\Tables;

abstract class UsageException extends \LogicException {}
abstract class RuntimeException extends \RuntimeException {}

final class ProbablyBrokenPrimaryIndexImplementation extends UsageException {
	public function __construct(Table $table, int $affectedRows)
	{
		parent::__construct(\sprintf(
			'Update of %s.%s led to updating more rows than expected (%d). Check the implementation of %s.',
			$table::getSchema(),
			$table::getTableName(),
			$affectedRows,
			$table::getPrimaryKeyClass()
		));
	}
};

final class CouldNotMapTypeException extends UsageException
{

	/**
	 * @param mixed $value
	 */
	public static function didYouRegisterTypeMapperFor(string $typeName, $value): self
	{
		return new self(
			"Did you register type mapper for type '$typeName' and value of type "
			. (\is_object($value) ? \get_class($value) : \gettype($value))
		);
	}
}

final class GivenSearchCriteriaHaveNotMatchedAnyRows extends RuntimeException {};

final class RowNotFound extends RuntimeException {};
