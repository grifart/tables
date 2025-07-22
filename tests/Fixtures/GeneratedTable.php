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
use Grifart\Tables\DefaultValue;
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
use Grifart\Tables\UnchangedValue;
use Nette\Utils\Paginator;
use const Grifart\Tables\DefaultValue;
use const Grifart\Tables\Unchanged;

final class GeneratedTable implements Table
{
	public const string ID = 'id';
	public const string DOUBLE = 'double';
	public const string DIRECT = 'direct';

	/** @var array{id: Column<self, int>, double: Column<self, int>, direct: Column<self, int>} */
	private array $columns;

	/** @var Column<self, int> */
	public Column $id {
		get => $this->columns['id'];
	}

	/** @var Column<self, int> */
	public Column $double {
		get => $this->columns['double'];
	}

	/** @var Column<self, int> */
	public Column $direct {
		get => $this->columns['direct'];
	}


	#[\Override]
	public static function getSchema(): string
	{
		return 'public';
	}


	#[\Override]
	public static function getTableName(): string
	{
		return 'generated';
	}


	#[\Override]
	public static function getPrimaryKeyClass(): string
	{
		return GeneratedPrimaryKey::class;
	}


	#[\Override]
	public static function getRowClass(): string
	{
		return GeneratedRow::class;
	}


	#[\Override]
	public static function getModificationClass(): string
	{
		return GeneratedModifications::class;
	}


	/**
	 * @return ColumnMetadata[]
	 */
	#[\Override]
	public static function getDatabaseColumns(): array
	{
		return [
			'id' => new ColumnMetadata('id', 'integer', false, true, true),
			'double' => new ColumnMetadata('double', 'integer', false, true, true),
			'direct' => new ColumnMetadata('direct', 'integer', false, false, false)
		];
	}


	public function find(GeneratedPrimaryKey $primaryKey): ?GeneratedRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: false);
		\assert($row instanceof GeneratedRow || $row === null);
		return $row;
	}


	/**
	 * @throws RowNotFound
	 */
	public function get(GeneratedPrimaryKey $primaryKey): GeneratedRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: true);
		\assert($row instanceof GeneratedRow);
		return $row;
	}


	/**
	 * @param OrderBy[] $orderBy
	 * @return GeneratedRow[]
	 */
	public function getAll(array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var GeneratedRow[] $result */
		$result = $this->tableManager->getAll($this, $orderBy, $paginator);
		return $result;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return GeneratedRow[]
	 */
	public function findBy(Condition|array $conditions, array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var GeneratedRow[] $result */
		$result = $this->tableManager->findBy($this, $conditions, $orderBy, $paginator);
		return $result;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 */
	public function count(Condition|array $conditions = []): int
	{
		return $this->tableManager->count($this, $conditions);
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return GeneratedRow
	 * @throws RowNotFound
	 */
	public function getUniqueBy(Condition|array $conditions): GeneratedRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: true, unique: true);
		\assert($row instanceof GeneratedRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return GeneratedRow|null
	 * @throws RowNotFound
	 */
	public function findUniqueBy(Condition|array $conditions): ?GeneratedRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: false, unique: true);
		\assert($row instanceof GeneratedRow || $row === null);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return GeneratedRow
	 * @throws RowNotFound
	 */
	public function getFirstBy(Condition|array $conditions, array $orderBy = []): GeneratedRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: true, unique: false);
		\assert($row instanceof GeneratedRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return GeneratedRow|null
	 */
	public function findFirstBy(Condition|array $conditions, array $orderBy = []): ?GeneratedRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: false, unique: false);
		\assert($row instanceof GeneratedRow || $row === null);
		return $row;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(GeneratedModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
	}


	public function new(int $direct): GeneratedModifications
	{
		$modifications = GeneratedModifications::new();
		$modifications->direct = $direct;
		return $modifications;
	}


	public function edit(
		GeneratedRow|GeneratedPrimaryKey $rowOrKey,
		int|UnchangedValue $direct = Unchanged,
	): GeneratedModifications
	{
		$primaryKey = $rowOrKey instanceof GeneratedPrimaryKey ? $rowOrKey : GeneratedPrimaryKey::fromRow($rowOrKey);
		$modifications = GeneratedModifications::update($primaryKey);
		if (!$direct instanceof UnchangedValue) {
			$modifications->direct = $direct;
		}
		return $modifications;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insert(int $direct): void
	{
		$modifications = GeneratedModifications::new();
		$modifications->direct = $direct;
		$this->tableManager->insert($this, $modifications);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insertAndGet(int $direct): GeneratedRow
	{
		$modifications = GeneratedModifications::new();
		$modifications->direct = $direct;
		$row = $this->tableManager->insertAndGet($this, $modifications);
		\assert($row instanceof GeneratedRow);
		return $row;
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function update(GeneratedRow|GeneratedPrimaryKey $rowOrKey, int|UnchangedValue $direct = Unchanged): void
	{
		$primaryKey = $rowOrKey instanceof GeneratedPrimaryKey ? $rowOrKey : GeneratedPrimaryKey::fromRow($rowOrKey);
		$modifications = GeneratedModifications::update($primaryKey);
		if (!$direct instanceof UnchangedValue) {
			$modifications->direct = $direct;
		}
		$this->tableManager->update($this, $modifications);
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function updateAndGet(
		GeneratedRow|GeneratedPrimaryKey $rowOrKey,
		int|UnchangedValue $direct = Unchanged,
	): GeneratedRow
	{
		$primaryKey = $rowOrKey instanceof GeneratedPrimaryKey ? $rowOrKey : GeneratedPrimaryKey::fromRow($rowOrKey);
		$modifications = GeneratedModifications::update($primaryKey);
		if (!$direct instanceof UnchangedValue) {
			$modifications->direct = $direct;
		}
		$row = $this->tableManager->updateAndGet($this, $modifications);
		\assert($row instanceof GeneratedRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 */
	public function updateBy(Condition|array $conditions, int|UnchangedValue $direct = Unchanged): void
	{
		$modifications = GeneratedModifications::new();
		if (!$direct instanceof UnchangedValue) {
			$modifications->direct = $direct;
		}
		$this->tableManager->updateBy($this, $conditions, $modifications);
	}


	public function upsert(int $direct): void
	{
		$modifications = GeneratedModifications::new();
		$modifications->direct = $direct;
		$this->tableManager->upsert($this, $modifications);
	}


	public function upsertAndGet(int $direct): GeneratedRow
	{
		$modifications = GeneratedModifications::new();
		$modifications->direct = $direct;
		$row = $this->tableManager->upsertAndGet($this, $modifications);
		\assert($row instanceof GeneratedRow);
		return $row;
	}


	public function delete(GeneratedRow|GeneratedPrimaryKey $rowOrKey): void
	{
		$primaryKey = $rowOrKey instanceof GeneratedPrimaryKey ? $rowOrKey : GeneratedPrimaryKey::fromRow($rowOrKey);
		$this->tableManager->delete($this, $primaryKey);
	}


	public function deleteAndGet(GeneratedRow|GeneratedPrimaryKey $rowOrKey): GeneratedRow
	{
		$primaryKey = $rowOrKey instanceof GeneratedPrimaryKey ? $rowOrKey : GeneratedPrimaryKey::fromRow($rowOrKey);
		$row = $this->tableManager->deleteAndGet($this, $primaryKey);
		\assert($row instanceof GeneratedRow);
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
		/** @var Column<self, int> $id */
		$id = Column::from($this, self::getDatabaseColumns()['id'], $this->typeResolver);
		/** @var Column<self, int> $double */
		$double = Column::from($this, self::getDatabaseColumns()['double'], $this->typeResolver);
		/** @var Column<self, int> $direct */
		$direct = Column::from($this, self::getDatabaseColumns()['direct'], $this->typeResolver);
		$this->columns = ['id' => $id, 'double' => $double, 'direct' => $direct];
	}


	/**
	 * @return Column<self, int>
	 */
	#[\Deprecated('Use $id property instead')]
	public function id(): Column
	{
		return $this->columns['id'];
	}


	/**
	 * @return Column<self, int>
	 */
	#[\Deprecated('Use $double property instead')]
	public function double(): Column
	{
		return $this->columns['double'];
	}


	/**
	 * @return Column<self, int>
	 */
	#[\Deprecated('Use $direct property instead')]
	public function direct(): Column
	{
		return $this->columns['direct'];
	}


	/**
	 * @internal
	 * @return Type<mixed>
	 */
	#[\Override]
	public function getTypeOf(string $columnName): Type
	{
		$column = $this->columns[$columnName] ?? throw ColumnNotFound::of($columnName, \get_class($this));
		/** @var Type<mixed> $type */
		$type = $column->getType();
		return $type;
	}
}
