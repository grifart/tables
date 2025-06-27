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
	public const string ID = 'id';
	public const string VALUE = 'value';
	public const string FLAGGED = 'flagged';

	/** @var array{id: Column<self, Uuid>, value: Column<self, int>, flagged: Column<self, bool>} */
	private array $columns;

	/** @var Column<self, Uuid> */
	public Column $id {
		get => $this->columns['id'];
	}

	/** @var Column<self, int> */
	public Column $value {
		get => $this->columns['value'];
	}

	/** @var Column<self, bool> */
	public Column $flagged {
		get => $this->columns['flagged'];
	}


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


	public function countAll(): int
	{
		return $this->tableManager->countAll($this);
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
	 */
	public function countBy(Condition|array $conditions): int
	{
		return $this->tableManager->countBy($this, $conditions);
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
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(BulkModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
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
	 */
	public function insert(
		Uuid $id,
		int $value,
		bool|DefaultOrExistingValue $flagged = \Grifart\Tables\DefaultValue,
	): void
	{
		$modifications = BulkModifications::new();
		$modifications->modifyId($id);
		$modifications->modifyValue($value);
		if (!$flagged instanceof DefaultOrExistingValue) {
			$modifications->modifyFlagged($flagged);
		}
		$this->tableManager->insert($this, $modifications);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insertAndGet(
		Uuid $id,
		int $value,
		bool|DefaultOrExistingValue $flagged = \Grifart\Tables\DefaultValue,
	): BulkRow
	{
		$modifications = BulkModifications::new();
		$modifications->modifyId($id);
		$modifications->modifyValue($value);
		if (!$flagged instanceof DefaultOrExistingValue) {
			$modifications->modifyFlagged($flagged);
		}
		$row = $this->tableManager->insertAndGet($this, $modifications);
		\assert($row instanceof BulkRow);
		return $row;
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function update(
		BulkRow|BulkPrimaryKey $rowOrKey,
		Uuid|DefaultOrExistingValue $id = \Grifart\Tables\Unchanged,
		int|DefaultOrExistingValue $value = \Grifart\Tables\Unchanged,
		bool|DefaultOrExistingValue $flagged = \Grifart\Tables\Unchanged,
	): void
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
		$this->tableManager->update($this, $modifications);
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function updateAndGet(
		BulkRow|BulkPrimaryKey $rowOrKey,
		Uuid|DefaultOrExistingValue $id = \Grifart\Tables\Unchanged,
		int|DefaultOrExistingValue $value = \Grifart\Tables\Unchanged,
		bool|DefaultOrExistingValue $flagged = \Grifart\Tables\Unchanged,
	): BulkRow
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
		$row = $this->tableManager->updateAndGet($this, $modifications);
		\assert($row instanceof BulkRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 */
	public function updateBy(
		Condition|array $conditions,
		Uuid|DefaultOrExistingValue $id = \Grifart\Tables\Unchanged,
		int|DefaultOrExistingValue $value = \Grifart\Tables\Unchanged,
		bool|DefaultOrExistingValue $flagged = \Grifart\Tables\Unchanged,
	): void
	{
		$modifications = BulkModifications::new();
		if (!$id instanceof DefaultOrExistingValue) {
			$modifications->modifyId($id);
		}
		if (!$value instanceof DefaultOrExistingValue) {
			$modifications->modifyValue($value);
		}
		if (!$flagged instanceof DefaultOrExistingValue) {
			$modifications->modifyFlagged($flagged);
		}
		$this->tableManager->updateBy($this, $conditions, $modifications);
	}


	public function upsert(
		Uuid $id,
		int $value,
		bool|DefaultOrExistingValue $flagged = \Grifart\Tables\DefaultValue,
	): void
	{
		$modifications = BulkModifications::new();
		$modifications->modifyId($id);
		$modifications->modifyValue($value);
		if (!$flagged instanceof DefaultOrExistingValue) {
			$modifications->modifyFlagged($flagged);
		}
		$this->tableManager->upsert($this, $modifications);
	}


	public function upsertAndGet(
		Uuid $id,
		int $value,
		bool|DefaultOrExistingValue $flagged = \Grifart\Tables\DefaultValue,
	): BulkRow
	{
		$modifications = BulkModifications::new();
		$modifications->modifyId($id);
		$modifications->modifyValue($value);
		if (!$flagged instanceof DefaultOrExistingValue) {
			$modifications->modifyFlagged($flagged);
		}
		$row = $this->tableManager->upsertAndGet($this, $modifications);
		\assert($row instanceof BulkRow);
		return $row;
	}


	public function delete(BulkRow|BulkPrimaryKey $rowOrKey): void
	{
		$primaryKey = $rowOrKey instanceof BulkPrimaryKey ? $rowOrKey : BulkPrimaryKey::fromRow($rowOrKey);
		$this->tableManager->delete($this, $primaryKey);
	}


	public function deleteAndGet(BulkRow|BulkPrimaryKey $rowOrKey): BulkRow
	{
		$primaryKey = $rowOrKey instanceof BulkPrimaryKey ? $rowOrKey : BulkPrimaryKey::fromRow($rowOrKey);
		$row = $this->tableManager->deleteAndGet($this, $primaryKey);
		\assert($row instanceof BulkRow);
		return $row;
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
	#[\Deprecated('Use $id property instead')]
	public function id(): Column
	{
		return $this->columns['id'];
	}


	/**
	 * @return Column<self, int>
	 */
	#[\Deprecated('Use $value property instead')]
	public function value(): Column
	{
		return $this->columns['value'];
	}


	/**
	 * @return Column<self, bool>
	 */
	#[\Deprecated('Use $flagged property instead')]
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
