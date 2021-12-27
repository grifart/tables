<?php

declare(strict_types=1);

namespace Grifart\Tables;

/**
 * @template TableType of Table
 * @template ValueType
 */
final class Column
{
	/**
	 * @param Type<ValueType> $resolvedType
	 */
	private function __construct(
		private ColumnMetadata $column,
		private Type $resolvedType,
	) {}

	public function getName(): string
	{
		return $this->column->getName();
	}

	/**
	 * @return Type<ValueType>
	 */
	public function getType(): Type
	{
		return $this->resolvedType;
	}

	/**
	 * @param ValueType|null $value
	 */
	public function map(mixed $value): mixed
	{
		if ($value === null) {
			return null;
		}

		return $this->resolvedType->toDatabase($value);
	}

	public function __toString(): string
	{
		return $this->column->getName();
	}

	/**
	 * @template FromTableType of Table
	 * @param FromTableType $table
	 * @return Column<FromTableType, mixed>
	 */
	public static function from(
		Table $table,
		ColumnMetadata $column,
		TypeResolver $typeResolver,
	): self
	{
		$location = "{$table::getSchema()}.{$table::getTableName()}.{$column->getName()}";
		$resolvedType = $typeResolver->resolveType($column->getType(), $location);

		/** @var Column<FromTableType, mixed> $column */
		$column = new self($column, $resolvedType);
		return $column;
	}
}
