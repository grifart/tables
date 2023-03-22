<?php declare(strict_types=1);

namespace Grifart\Tables;

abstract class UsageException extends \LogicException {}
abstract class RuntimeException extends \RuntimeException {}

final class MissingPrimaryIndex extends UsageException
{
	public static function in(string $schema, string $table): self
	{
		return new self(\sprintf('Table "%s"."%s" must have a primary index. Provide one and try again.', $schema, $table));
	}
}

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

final class MissingDatabaseTypeResolution extends UsageException
{
	/**
	 * @param Type<mixed> $type
	 */
	public static function of(Type $type): self
	{
		return new self(\sprintf('Cannot auto-register type %s, it does not declare any matching database types.', \get_class($type)));
	}
}

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

final class ColumnNotFound extends UsageException {
	public static function of(string $columnName, string $tableClassName): self
	{
		return new self("Column '$columnName' not found in '$tableClassName'.");
	}
}

final class GivenSearchCriteriaHaveNotMatchedAnyRows extends RuntimeException {}

final class RowNotFound extends RuntimeException {}
final class TooManyRowsFound extends UsageException {}
