<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\Connection;
use Grifart\Tables\Conditions\Composite;
use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\OrderBy\OrderBy;
use function Functional\map;

// todo: error handling
// todo: mapping of exceptions
// todo: paging/limit (needed?)

final class TableManager
{

	public function __construct(
		private Connection $connection,
	) {}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 */
	public function insert(Table $table, Modifications $changes): void
	{
		\assert($changes->getPrimaryKey() === NULL);
		$this->connection->query(
			'INSERT',
			'INTO %n.%n', $table::getSchema(), $table::getTableName(),
			map(
				$changes->getModifications(),
				static fn(mixed $value, string $columnName) => $value !== null ? $table->getTypeOf($columnName)->toDatabase($value) : null,
			),
		);
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
			\assert($row instanceof Row, 'It cannot return false as there must be one element in array');
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
	public function getAll(Table $table, array $orderBy = []): array
	{
		return $this->findBy($table, [], $orderBy);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return Row[] (subclass of row)
	 */
	public function findBy(Table $table, Condition|array $conditions, array $orderBy = []): array
	{
		$result = $this->connection->query(
			'SELECT *',
			'FROM %n.%n', $table::getSchema(), $table::getTableName(),
			'WHERE %ex', (\is_array($conditions) ? Composite::and(...$conditions) : $conditions)->format(),
			'ORDER BY %by', \count($orderBy) > 0
				? map($orderBy, function (OrderBy|Expression $orderBy) {
					if ($orderBy instanceof Expression) {
						$orderBy = new OrderBy($orderBy);
					}

					return $orderBy->format();
				})
				: [['%b', true]],
		);

		foreach ($table::getDatabaseColumns() as $column) {
			$result->setType($column->getName(), NULL);
		}

		$dibiRows = $result->fetchAll();

		/** @var Row $rowClass */
		$rowClass = $table::getRowClass();
		$modelRows = [];
		foreach ($dibiRows as $dibiRow) {
			\assert($dibiRow instanceof \Dibi\Row);
			$modelRows[] = $rowClass::reconstitute(
				map(
					$dibiRow->toArray(),
					static fn(mixed $value, string $columnName) => $value !== null ? $table->getTypeOf($columnName)->fromDatabase($value) : null,
				),
			);
		}
		return $modelRows;
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
			map(
				$changes->getModifications(),
				static fn(mixed $value, string $columnName) => $value !== null ? $table->getTypeOf($columnName)->toDatabase($value) : null,
			),
			'WHERE %ex', $primaryKey->getCondition($table)->format(),
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
			'WHERE %ex', $primaryKey->getCondition($table)->format(),
		);
		\assert($this->connection->getAffectedRows() === 1);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
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
