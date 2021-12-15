<?php

declare(strict_types=1);

namespace Grifart\Tables;

final class Column
{
	private function __construct(
		private ColumnMetadata $column,
		private Type $resolvedType,
	) {}

	public function getName(): string
	{
		return $this->column->getName();
	}

	public function getType(): Type
	{
		return $this->resolvedType;
	}

	public function map(mixed $value): mixed
	{
		return $this->resolvedType->toDatabase($value);
	}

	public function __toString(): string
	{
		return $this->column->getName();
	}

	public static function from(
		Table $table,
		ColumnMetadata $column,
		TypeResolver $typeResolver,
	): self
	{
		$location = "{$table::getSchema()}.{$table::getTableName()}.{$column->getName()}";
		$resolvedType = $typeResolver->resolveType($column->getType(), $location);

		$column = new self($column, $resolvedType);
		return $column;
	}
}
