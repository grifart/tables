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

final class TypeAlreadyRegistered extends UsageException
{
	public static function forDatabaseType(string $typeName): self
	{
		return new self("Cannot add resolution, a Type is already registered for database type '$typeName'.");
	}

	public static function forLocation(string $location): self
	{
		return new self("Cannot add resolution, a Type is already registered for location '$location'.");
	}
}

final class UnresolvableType extends UsageException
{
	public static function of(string $location, string $typeName): self
	{
		return new self("No Type is registered either for location '$location' or database type '$typeName'.");
	}
}

final class GivenSearchCriteriaHaveNotMatchedAnyRows extends RuntimeException {};

final class RowNotFound extends RuntimeException {};
