<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Expression as DibiExpression;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Types\NullableType;

/**
 * @template TableType of Table
 * @template ValueType
 * @extends ExpressionWithShorthands<ValueType>
 */
final class Column extends ExpressionWithShorthands
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

	public function toSql(): DibiExpression
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
		$location = new Identifier($table::getSchema(), $table::getTableName(), $column->getName());
		$resolvedType = $typeResolver->resolveType($column->getType(), $location);

		if ($column->isNullable()) {
			$resolvedType = NullableType::of($resolvedType);
		}

		/** @var Column<FromTableType, mixed> $column */
		$column = new self($column, $resolvedType);
		return $column;
	}
}
