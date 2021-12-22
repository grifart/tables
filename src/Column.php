<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Driver;
use Dibi\Expression as DibiExpression;
use Dibi\Literal;
use Grifart\Tables\Conditions\SingleCondition;
use Grifart\Tables\Conditions\Operation;

/**
 * @template TableType of Table
 * @template ValueType
 * @implements Expression<ValueType>
 */
final class Column implements Expression
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

	public function toSql(): DibiExpression|Literal
	{
		return new DibiExpression('%n', $this->getName());
	}

	/**
	 * @return Type<ValueType>
	 */
	public function getType(): Type
	{
		return $this->resolvedType;
	}

	/**
	 * @param Operation<ValueType> $operation
	 * @return SingleCondition<ValueType>
	 */
	public function is(Operation $operation): SingleCondition
	{
		return new SingleCondition($this, $operation);
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
