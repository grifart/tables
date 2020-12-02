<?php declare(strict_types=1);


namespace Grifart\Tables;


use Dibi\Connection;

// todo: error handling
// todo: mapping of primary key!
// todo: mapping of where() clause
// todo: mapping of orderBy() clause
// todo: mapping of exceptions

final class TableManager
{

	public function __construct(
		private Connection $connection,
		private TypeMapper $phpToDatabaseMapper,
		private TypeMapper $databaseToPhpMapper,
	) {}


	public function insert(Table $table, Modifications $changes): void
	{
		\assert($changes->getPrimaryKey() === NULL);
		$this->connection->query(
			'INSERT',
			'INTO %n.%n', $table::getSchema(), $table::getTableName(),
			self::mapTypes($this->phpToDatabaseMapper, $changes->getModifications(), $table)
		);
		\assert($this->connection->getAffectedRows() === 1);
	}

	/** @return null|Row (subclass of row) */
	public function find(Table $table, PrimaryKey $primaryKey) {
		$rows = $this->findBy($table, $primaryKey->getQuery());
		if (\count($rows) === 1) {
			$row = \reset($rows);
			\assert($row instanceof Row, 'It cannot return false as there must be one element in array');
			return $row;
		}
		\assert(\count($rows) === 0);
		return NULL;
	}

	/**
	 * @param array<string, mixed> $conditions Conditions provides low-level access to "where" clause concatenated by %and. 
	 *                                         More: https://dibiphp.com/en/documentation
	 *                                         Note! Types and names are currently NOT mapped.
	 *                                         Please follow https://gitlab.grifart.cz/grifart/tables/-/issues/2 on progress.
	 * @return Row[] (subclass of row)
	 */
	public function findBy(Table $table, array $conditions): array
	{
		$result = $this->connection->query(
			'SELECT *',
			'FROM %n.%n', $table::getSchema(), $table::getTableName(),
			'WHERE %and', \count($conditions) === 0 ? ['true'] : $conditions
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
				self::mapTypes($this->databaseToPhpMapper, $dibiRow->toArray(), $table)
			);
		}
		return $modelRows;
	}

	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows if no rows matches given criteria
	 */
	public function update(Table $table, Modifications $changes): void
	{
		$primaryKey = $changes->getPrimaryKey();
		\assert($primaryKey !== NULL);
		$this->connection->query(
			'UPDATE %n.%n', $table::getSchema(), $table::getTableName(),
			'SET %a', self::mapTypes($this->phpToDatabaseMapper, $changes->getModifications(), $table),
			'WHERE %and', $primaryKey->getQuery()
		);
		$affectedRows = $this->connection->getAffectedRows();
		if ($affectedRows !== 1) {
			if ($affectedRows === 0) {
				throw new GivenSearchCriteriaHaveNotMatchedAnyRows();
			}

			throw new ProbablyBrokenPrimaryIndexImplementation($table, $affectedRows);
		}
	}

	public function delete(Table $table, PrimaryKey $primaryKey): void
	{
		$this->connection->query(
			'DELETE',
			'FROM %n.%n', $table::getSchema(), $table::getTableName(),
			'WHERE %and', $primaryKey->getQuery()
		);
		\assert($this->connection->getAffectedRows() === 1);
	}


	/**
	 * @param array<string, mixed> $values
	 * @return array<string, mixed>
	 */
	private static function mapTypes(TypeMapper $mapper, array $values, Table $table): array
	{
		// could not use array_map as it does not preserve indexes
		$mappedValues = [];
		foreach ($values as $columnName => $value) {
			$mappedValues[$columnName] = $mapper->map(
				self::locationForColumn($table, $columnName),
				$table::getDatabaseColumns()[$columnName]->getType(),
				$value
			);
		}
		return $mappedValues;
	}

	private static function locationForColumn(Table $table, string $columnName): string {
		return $table::getSchema() . '.' . $table::getTableName() . '.' . $columnName;
	}

	/**
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
