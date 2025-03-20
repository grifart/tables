<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Column;
use Grifart\Tables\ColumnMetadata;
use Grifart\Tables\ColumnNotFound;
use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\DefaultOrExistingValue;
use Grifart\Tables\Expression;
use Grifart\Tables\GivenSearchCriteriaHaveNotMatchedAnyRows;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\RowNotFound;
use Grifart\Tables\RowWithGivenPrimaryKeyAlreadyExists;
use Grifart\Tables\Table;
use Grifart\Tables\TableManager;
use Grifart\Tables\TooManyRowsFound;
use Grifart\Tables\Type;
use Grifart\Tables\TypeResolver;
use Nette\Utils\Paginator;

final class BulkTable implements Table
{
	public const ID = 'id';
	public const VALUE = 'value';
	public const FLAGGED = 'flagged';

	/** @var array{id: Column<self, Uuid>, value: Column<self, int>, flagged: Column<self, bool>} */
	private array $columns;


	public static function getSchema(): string
	{
		return 'public';
	}


	public static function getTableName(): string
	{
		return 'bulk';
	}


	public static function getPrimaryKeyClass(): string
	{
		return BulkPrimaryKey::class;
	}


	public static function getRowClass(): string
	{
		return BulkRow::class;
	}


	public static function getModificationClass(): string
	{
		return BulkModifications::class;
	}


	/**
	 * @return ColumnMetadata[]
	 */
	public static function getDatabaseColumns(): array
	{
		return [
			'id' => new ColumnMetadata('id', 'uuid', false, false, false),
			'value' => new ColumnMetadata('value', 'integer', false, false, false),
			'flagged' => new ColumnMetadata('flagged', 'boolean', false, true, false)
		];
	}


	public function find(BulkPrimaryKey $primaryKey): ?BulkRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: false);
		\assert($row instanceof BulkRow || $row === null);
		return $row;
	}


	/**
	 * @throws RowNotFound
	 */
	public function get(BulkPrimaryKey $primaryKey): BulkRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: true);
		\assert($row instanceof BulkRow);
		return $row;
	}


	/**
	 * @param OrderBy[] $orderBy
	 * @return BulkRow[]
	 */
	public function getAll(array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var BulkRow[] $result */
		$result = $this->tableManager->getAll($this, $orderBy, $paginator);
		return $result;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return BulkRow[]
	 */
	public function findBy(Condition|array $conditions, array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var BulkRow[] $result */
		$result = $this->tableManager->findBy($this, $conditions, $orderBy, $paginator);
		return $result;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return BulkRow
	 * @throws RowNotFound
	 */
	public function getUniqueBy(Condition|array $conditions): BulkRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: true, unique: true);
		\assert($row instanceof BulkRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return BulkRow|null
	 * @throws RowNotFound
	 */
	public function findUniqueBy(Condition|array $conditions): ?BulkRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: false, unique: true);
		\assert($row instanceof BulkRow || $row === null);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return BulkRow
	 * @throws RowNotFound
	 */
	public function getFirstBy(Condition|array $conditions, array $orderBy = []): BulkRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: true, unique: false);
		\assert($row instanceof BulkRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return BulkRow|null
	 */
	public function findFirstBy(Condition|array $conditions, array $orderBy = []): ?BulkRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: false, unique: false);
		\assert($row instanceof BulkRow || $row === null);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return BulkRow
	 * @throws RowNotFound
	 */
	#[\Deprecated('Use getUniqueBy() instead.')]
	public function getBy(Condition|array $conditions): BulkRow
	{
		return $this->getUniqueBy($conditions);
	}


	public function new(
		Uuid $id,
		int $value,
		bool|DefaultOrExistingValue $flagged = \Grifart\Tables\DefaultValue,
	): BulkModifications
	{
		$modifications = BulkModifications::new();
		$modifications->modifyId($id);
		$modifications->modifyValue($value);
		if (!$flagged instanceof DefaultOrExistingValue) {
			$modifications->modifyFlagged($flagged);
		}
		return $modifications;
	}


	public function edit(
		BulkRow|BulkPrimaryKey $rowOrKey,
		Uuid|DefaultOrExistingValue $id = \Grifart\Tables\Unchanged,
		int|DefaultOrExistingValue $value = \Grifart\Tables\Unchanged,
		bool|DefaultOrExistingValue $flagged = \Grifart\Tables\Unchanged,
	): BulkModifications
	{
		$primaryKey = $rowOrKey instanceof BulkPrimaryKey ? $rowOrKey : BulkPrimaryKey::fromRow($rowOrKey);
		$modifications = BulkModifications::update($primaryKey);
		if (!$id instanceof DefaultOrExistingValue) {
			$modifications->modifyId($id);
		}
		if (!$value instanceof DefaultOrExistingValue) {
			$modifications->modifyValue($value);
		}
		if (!$flagged instanceof DefaultOrExistingValue) {
			$modifications->modifyFlagged($flagged);
		}
		return $modifications;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(BulkModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insert(BulkModifications $changes): void
	{
		$this->tableManager->insert($this, $changes);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insertAndGet(BulkModifications $changes): BulkRow
	{
		$row = $this->tableManager->insertAndGet($this, $changes);
		\assert($row instanceof BulkRow);
		return $row;
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function update(BulkModifications $changes): void
	{
		$this->tableManager->update($this, $changes);
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function updateAndGet(BulkModifications $changes): BulkRow
	{
		$row = $this->tableManager->updateAndGet($this, $changes);
		\assert($row instanceof BulkRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 */
	public function updateBy(Condition|array $conditions, BulkModifications $changes): void
	{
		$this->tableManager->updateBy($this, $conditions, $changes);
	}


	public function delete(BulkRow|BulkPrimaryKey $rowOrKey): void
	{
		$primaryKey = $rowOrKey instanceof BulkPrimaryKey ? $rowOrKey : BulkPrimaryKey::fromRow($rowOrKey);
		$this->tableManager->delete($this, $primaryKey);
	}


	/**
	 * @param Condition|Condition[] $conditions
	 */
	public function deleteBy(Condition|array $conditions): void
	{
		$this->tableManager->deleteBy($this, $conditions);
	}


	public function __construct(
		private TableManager $tableManager,
		private TypeResolver $typeResolver,
	) {
		/** @var Column<self, Uuid> $id */
		$id = Column::from($this, self::getDatabaseColumns()['id'], $this->typeResolver);
		/** @var Column<self, int> $value */
		$value = Column::from($this, self::getDatabaseColumns()['value'], $this->typeResolver);
		/** @var Column<self, bool> $flagged */
		$flagged = Column::from($this, self::getDatabaseColumns()['flagged'], $this->typeResolver);
		$this->columns = ['id' => $id, 'value' => $value, 'flagged' => $flagged];
	}


	/**
	 * @return Column<self, Uuid>
	 */
	public function id(): Column
	{
		return $this->columns['id'];
	}


	/**
	 * @return Column<self, int>
	 */
	public function value(): Column
	{
		return $this->columns['value'];
	}


	/**
	 * @return Column<self, bool>
	 */
	public function flagged(): Column
	{
		return $this->columns['flagged'];
	}


	/**
	 * @internal
	 * @return Type<mixed>
	 */
	public function getTypeOf(string $columnName): Type
	{
		$column = $this->columns[$columnName] ?? throw ColumnNotFound::of($columnName, \get_class($this));
		/** @var Type<mixed> $type */
		$type = $column->getType();
		return $type;
	}
}
