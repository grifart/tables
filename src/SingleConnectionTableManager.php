<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\IConnection;
use Dibi\UniqueConstraintViolationException;
use Grifart\Tables\Conditions\Composite;
use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\OrderBy\OrderByDirection;
use Grifart\Tables\Table as TableType;
use Nette\Utils\Paginator;
use function Phun\map;
use function Phun\mapWithKeys;

// todo: error handling
// todo: mapping of exceptions

final class SingleConnectionTableManager implements TableManager
{

	public function __construct(
		private IConnection $connection,
	) {}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param PrimaryKey<TableType> $primaryKey
	 * @return ($required is true ? Row : Row|null)
	 * @throws RowNotFound
	 */
	public function find(Table $table, PrimaryKey $primaryKey, bool $required = true): ?Row
	{
		return $this->findOneBy($table, $primaryKey->getCondition($table), required: $required);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param array<OrderBy|Expression<mixed>> $orderBy
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
					static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->fromDatabase($value),
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
	 * @return ($required is true ? Row : Row|null)
	 * @throws RowNotFound
	 */
	public function findOneBy(Table $table, Condition|array $conditions, array $orderBy = [], bool $required = true, bool $unique = true): ?Row
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
		);

		foreach ($table::getDatabaseColumns() as $column) {
			$result->setType($column->getName(), NULL);
		}

		$dibiRow = $result->fetch();
		if ($dibiRow === null) {
			return ! $required ? null : throw new RowNotFound();
		}

		if ($unique && $result->fetch() !== null) {
			throw new TooManyRowsFound();
		}

		/** @var class-string<Row> $rowClass */
		$rowClass = $table::getRowClass();
		\assert($dibiRow instanceof \Dibi\Row);
		return $rowClass::reconstitute(
			mapWithKeys(
				$dibiRow->toArray(),
				static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->fromDatabase($value),
			),
		);
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
					static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->toDatabase($value),
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
	 * @param Modifications<TableType> $changes
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insertAndGet(Table $table, Modifications $changes): Row
	{
		\assert($changes->getPrimaryKey() === NULL);

		try {
			$result = $this->connection->query(
				'INSERT INTO %n.%n', $table::getSchema(), $table::getTableName(),
				mapWithKeys(
					$changes->getModifications(),
					static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->toDatabase($value),
				),
				'RETURNING *',
			);
		} catch (UniqueConstraintViolationException $e) {
			throw new RowWithGivenPrimaryKeyAlreadyExists(previous: $e);
		}

		\assert($this->connection->getAffectedRows() === 1);

		$dibiRow = $result->fetch();
		\assert($dibiRow instanceof \Dibi\Row);

		/** @var class-string<Row> $rowClass */
		$rowClass = $table::getRowClass();
		return $rowClass::reconstitute(
			mapWithKeys(
				$dibiRow->toArray(),
				static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->fromDatabase($value),
			),
		);
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
				static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->toDatabase($value),
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
	 * @param Modifications<TableType> $changes
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function updateAndGet(Table $table, Modifications $changes): Row
	{
		$primaryKey = $changes->getPrimaryKey();
		\assert($primaryKey !== NULL);
		$result = $this->connection->query(
			'UPDATE %n.%n', $table::getSchema(), $table::getTableName(),
			'SET %a',
			mapWithKeys(
				$changes->getModifications(),
				static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->toDatabase($value),
			),
			'WHERE %ex', $primaryKey->getCondition($table)->toSql()->getValues(),
			'RETURNING *',
		);

		$affectedRows = $this->connection->getAffectedRows();
		if ($affectedRows !== 1) {
			if ($affectedRows === 0) {
				throw new GivenSearchCriteriaHaveNotMatchedAnyRows();
			}

			throw new ProbablyBrokenPrimaryIndexImplementation($table, $affectedRows);
		}

		$dibiRow = $result->fetch();
		\assert($dibiRow instanceof \Dibi\Row);

		/** @var class-string<Row> $rowClass */
		$rowClass = $table::getRowClass();
		return $rowClass::reconstitute(
			mapWithKeys(
				$dibiRow->toArray(),
				static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->fromDatabase($value),
			),
		);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Condition|Condition[] $conditions
	 * @param Modifications<TableType> $changes
	 */
	public function updateBy(Table $table, Condition|array $conditions, Modifications $changes): void
	{
		\assert($changes->getPrimaryKey() === null);

		$this->connection->query(
			'UPDATE %n.%n', $table::getSchema(), $table::getTableName(),
			'SET %a',
			mapWithKeys(
				$changes->getModifications(),
				static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->toDatabase($value),
			),
			'WHERE %ex', (\is_array($conditions) ? Composite::and(...$conditions) : $conditions)->toSql()->getValues(),
		);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 */
	public function upsert(Table $table, Modifications $changes): void
	{
		\assert($changes->getPrimaryKey() === null);

		$values = mapWithKeys(
			$changes->getModifications(),
			static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->toDatabase($value),
		);

		$primaryKey = $table::getPrimaryKeyClass();

		$this->connection->query(
			'INSERT INTO %n.%n', $table::getSchema(), $table::getTableName(),
			$values,
			'ON CONFLICT (%n)', $primaryKey::getColumnNames(),
			'DO UPDATE SET %a', $values,
		);

		\assert($this->connection->getAffectedRows() === 1);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Modifications<TableType> $changes
	 */
	public function upsertAndGet(Table $table, Modifications $changes): Row
	{
		\assert($changes->getPrimaryKey() === null);

		$values = mapWithKeys(
			$changes->getModifications(),
			static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->toDatabase($value),
		);

		$primaryKey = $table::getPrimaryKeyClass();

		$result = $this->connection->query(
			'INSERT INTO %n.%n', $table::getSchema(), $table::getTableName(),
			$values,
			'ON CONFLICT (%n)', $primaryKey::getColumnNames(),
			'DO UPDATE SET %a', $values,
			'RETURNING *',
		);

		\assert($this->connection->getAffectedRows() === 1);

		$dibiRow = $result->fetch();
		\assert($dibiRow instanceof \Dibi\Row);

		/** @var class-string<Row> $rowClass */
		$rowClass = $table::getRowClass();
		return $rowClass::reconstitute(
			mapWithKeys(
				$dibiRow->toArray(),
				static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->fromDatabase($value),
			),
		);
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
	 * @param PrimaryKey<TableType> $primaryKey
	 */
	public function deleteAndGet(Table $table, PrimaryKey $primaryKey): Row
	{
		$result = $this->connection->query(
			'DELETE',
			'FROM %n.%n', $table::getSchema(), $table::getTableName(),
			'WHERE %ex', $primaryKey->getCondition($table)->toSql()->getValues(),
			'RETURNING *',
		);

		\assert($this->connection->getAffectedRows() === 1);

		$dibiRow = $result->fetch();
		\assert($dibiRow instanceof \Dibi\Row);

		/** @var class-string<Row> $rowClass */
		$rowClass = $table::getRowClass();
		return $rowClass::reconstitute(
			mapWithKeys(
				$dibiRow->toArray(),
				static fn(string $columnName, mixed $value) => $table->getTypeOf($columnName)->fromDatabase($value),
			),
		);
	}

	/**
	 * @template TableType of Table
	 * @param TableType $table
	 * @param Condition|Condition[] $conditions
	 */
	public function deleteBy(Table $table, Condition|array $conditions): void
	{
		$this->connection->query(
			'DELETE',
			'FROM %n.%n', $table::getSchema(), $table::getTableName(),
			'WHERE %ex', (\is_array($conditions) ? Composite::and(...$conditions) : $conditions)->toSql()->getValues(),
		);
	}
}
