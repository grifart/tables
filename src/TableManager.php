<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\Table as TableType;
use Nette\Utils\Paginator;

interface TableManager
{
	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param PrimaryKey<TableType> $primaryKey
	 * @return ($required is true ? Row : Row|null)
	 * @throws RowNotFound
	 */
	public function find(Table $table, PrimaryKey $primaryKey, bool $required = true): ?Row;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return Row[]
	 */
	public function getAll(Table $table, array $orderBy = [], ?Paginator $paginator = null): array;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return Row[] (subclass of row)
	 */
	public function findBy(Table $table, Condition|array $conditions, array $orderBy = [], ?Paginator $paginator = null): array;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return ($required is true ? Row : Row|null)
	 * @throws RowNotFound
	 */
	public function findOneBy(Table $table, Condition|array $conditions, array $orderBy = [], bool $required = true, bool $unique = true): ?Row;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(Table $table, Modifications $changes): void;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insert(Table $table, Modifications $changes): void;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insertAndGet(Table $table, Modifications $changes): Row;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows if no rows matches given criteria
	 */
	public function update(Table $table, Modifications $changes): void;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows if no rows matches given criteria
	 */
	public function updateAndGet(Table $table, Modifications $changes): Row;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Condition|Condition[] $conditions
	 * @param Modifications<TableType> $changes
	 */
	public function updateBy(Table $table, Condition|array $conditions, Modifications $changes): void;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param PrimaryKey<TableType> $primaryKey
	 */
	public function delete(Table $table, PrimaryKey $primaryKey): void;

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Condition|Condition[] $conditions
	 */
	public function deleteBy(Table $table, Condition|array $conditions): void;
}
