<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Connection;
use Dibi\UniqueConstraintViolationException;
use Grifart\Tables\Conditions\Composite;
use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\OrderBy\OrderByDirection;
use Nette\Utils\Paginator;
use function Phun\map;
use function Phun\mapWithKeys;

// todo: error handling
// todo: mapping of exceptions

final class TableManager
{

	public function __construct(
		private Connection $connection,
	) {}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insert(Table $table, Modifications $changes): void
	{
		\assert($changes->getPrimaryKey() === NULL);

		try {
			$this->connection->query(
				'INSERT',
				'INTO %n.%n', $table::getSchema(), $table::getTableName(),
				mapWithKeys(
					$changes->getModifications(),
					static fn(string $columnName, mixed $value) => $value !== null ? $table->getTypeOf($columnName)->toDatabase($value) : null,
				),
			);
		} catch (UniqueConstraintViolationException $e) {
			throw new RowWithGivenPrimaryKeyAlreadyExists(previous: $e);
		}

		\assert($this->connection->getAffectedRows() === 1);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param PrimaryKey<TableType> $primaryKey
	 * @return null|Row
	 */
	public function find(Table $table, PrimaryKey $primaryKey): ?Row
	{
		$rows = $this->findBy($table, $primaryKey->getCondition($table));
		if (\count($rows) === 1) {
			$row = \reset($rows);
			return $row;
		}
		\assert(\count($rows) === 0);
		return NULL;
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param OrderBy[] $orderBy
	 * @return Row[]
	 */
	public function getAll(Table $table, array $orderBy = [], ?Paginator $paginator = null): array
	{
		return $this->findBy($table, [], $orderBy, $paginator);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return Row[] (subclass of row)
	 */
	public function findBy(Table $table, Condition|array $conditions, array $orderBy = [], ?Paginator $paginator = null): array
	{
		$result = $this->connection->query(
			'SELECT *',
			'FROM %n.%n', $table::getSchema(), $table::getTableName(),
			'WHERE %ex', (\is_array($conditions) ? Composite::and(...$conditions) : $conditions)->toSql()->getValues(),
			'ORDER BY %by', \count($orderBy) > 0
				? map($orderBy, function (OrderBy|Expression $orderBy) {
					if ($orderBy instanceof Expression) {
						$orderBy = new OrderByDirection($orderBy);
					}

					return $orderBy->toSql()->getValues();
				})
				: [['%sql', 'true::boolean']],
			'%lmt', $paginator?->getItemsPerPage(),
			'%ofs', $paginator?->getOffset(),
		);

		foreach ($table::getDatabaseColumns() as $column) {
			$result->setType($column->getName(), NULL);
		}

		$dibiRows = $result->fetchAll();

		/** @var class-string<Row> $rowClass */
		$rowClass = $table::getRowClass();
		$modelRows = [];
		foreach ($dibiRows as $dibiRow) {
			\assert($dibiRow instanceof \Dibi\Row);
			$modelRows[] = $rowClass::reconstitute(
				mapWithKeys(
					$dibiRow->toArray(),
					static fn(string $columnName, mixed $value) => $value !== null ? $table->getTypeOf($columnName)->fromDatabase($value) : null,
				),
			);
		}

		if ($paginator !== null) {
			$totalCount = $this->connection->query(
				'SELECT COUNT(*)',
				'FROM %n.%n', $table::getSchema(), $table::getTableName(),
				'WHERE %ex', (\is_array($conditions) ? Composite::and(...$conditions) : $conditions)->toSql()->getValues(),
			)->fetchSingle();

			\assert(\is_int($totalCount));
			$paginator->setItemCount($totalCount);
		}

		return $modelRows;
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return array{Row|null, int}
	 */
	public function findOneBy(Table $table, Condition|array $conditions, array $orderBy = [], bool $checkCount = true): array
	{
		$result = $this->connection->query(
			'SELECT *',
			'FROM %n.%n', $table::getSchema(), $table::getTableName(),
			'WHERE %ex', (\is_array($conditions) ? Composite::and(...$conditions) : $conditions)->toSql()->getValues(),
			'ORDER BY %by', \count($orderBy) > 0
				? map($orderBy, function (OrderBy|Expression $orderBy) {
					if ($orderBy instanceof Expression) {
						$orderBy = new OrderByDirection($orderBy);
					}

					return $orderBy->toSql()->getValues();
				})
				: [['%sql', 'true::boolean']],
			'%lmt', $checkCount ? 2 : 1,
		);

		foreach ($table::getDatabaseColumns() as $column) {
			$result->setType($column->getName(), NULL);
		}

		$dibiRows = $result->fetchAll();

		/** @var class-string<Row> $rowClass */
		$rowClass = $table::getRowClass();
		$modelRows = [];
		foreach ($dibiRows as $dibiRow) {
			\assert($dibiRow instanceof \Dibi\Row);
			$modelRows[] = $rowClass::reconstitute(
				mapWithKeys(
					$dibiRow->toArray(),
					static fn(string $columnName, mixed $value) => $value !== null ? $table->getTypeOf($columnName)->fromDatabase($value) : null,
				),
			);
		}

		return [
			$modelRows[0] ?? null,
			\count($modelRows),
		];
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows if no rows matches given criteria
	 */
	public function update(Table $table, Modifications $changes): void
	{
		$primaryKey = $changes->getPrimaryKey();
		\assert($primaryKey !== NULL);
		$this->connection->query(
			'UPDATE %n.%n', $table::getSchema(), $table::getTableName(),
			'SET %a',
			mapWithKeys(
				$changes->getModifications(),
				static fn(string $columnName, mixed $value) => $value !== null ? $table->getTypeOf($columnName)->toDatabase($value) : null,
			),
			'WHERE %ex', $primaryKey->getCondition($table)->toSql()->getValues(),
		);
		$affectedRows = $this->connection->getAffectedRows();
		if ($affectedRows !== 1) {
			if ($affectedRows === 0) {
				throw new GivenSearchCriteriaHaveNotMatchedAnyRows();
			}

			throw new ProbablyBrokenPrimaryIndexImplementation($table, $affectedRows);
		}
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param PrimaryKey<TableType> $primaryKey
	 */
	public function delete(Table $table, PrimaryKey $primaryKey): void
	{
		$this->connection->query(
			'DELETE',
			'FROM %n.%n', $table::getSchema(), $table::getTableName(),
			'WHERE %ex', $primaryKey->getCondition($table)->toSql()->getValues(),
		);
		\assert($this->connection->getAffectedRows() === 1);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(Table $table, Modifications $changes): void {
		if ($changes->getPrimaryKey() === NULL) {
			// INSERT
			$this->insert($table, $changes);
			return;
		}

		// UPDATE:
		$this->update($table, $changes);
	}
}
