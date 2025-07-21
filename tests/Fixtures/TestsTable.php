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

final class TestsTable implements Table
{
	public const string ID = 'id';
	public const string SCORE = 'score';
	public const string DETAILS = 'details';

	/** @var array{id: Column<self, Uuid>, score: Column<self, int>, details: Column<self, string|null>} */
	private array $columns;

	/** @var Column<self, Uuid> */
	public Column $id {
		get => $this->columns['id'];
	}

	/** @var Column<self, int> */
	public Column $score {
		get => $this->columns['score'];
	}

	/** @var Column<self, string|null> */
	public Column $details {
		get => $this->columns['details'];
	}


	#[\Override]
	public static function getSchema(): string
	{
		return 'public';
	}


	#[\Override]
	public static function getTableName(): string
	{
		return 'test';
	}


	#[\Override]
	public static function getPrimaryKeyClass(): string
	{
		return TestPrimaryKey::class;
	}


	#[\Override]
	public static function getRowClass(): string
	{
		return TestRow::class;
	}


	#[\Override]
	public static function getModificationClass(): string
	{
		return TestModifications::class;
	}


	/**
	 * @return ColumnMetadata[]
	 */
	#[\Override]
	public static function getDatabaseColumns(): array
	{
		return [
			'id' => new ColumnMetadata('id', 'uuid', false, false, false),
			'score' => new ColumnMetadata('score', 'integer', false, false, false),
			'details' => new ColumnMetadata('details', 'character varying', true, true, false)
		];
	}


	public function find(TestPrimaryKey $primaryKey): ?TestRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: false);
		\assert($row instanceof TestRow || $row === null);
		return $row;
	}


	/**
	 * @throws RowNotFound
	 */
	public function get(TestPrimaryKey $primaryKey): TestRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: true);
		\assert($row instanceof TestRow);
		return $row;
	}


	/**
	 * @param OrderBy[] $orderBy
	 * @return TestRow[]
	 */
	public function getAll(array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var TestRow[] $result */
		$result = $this->tableManager->getAll($this, $orderBy, $paginator);
		return $result;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return TestRow[]
	 */
	public function findBy(Condition|array $conditions, array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var TestRow[] $result */
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
	 * @return TestRow
	 * @throws RowNotFound
	 */
	public function getUniqueBy(Condition|array $conditions): TestRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: true, unique: true);
		\assert($row instanceof TestRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return TestRow|null
	 * @throws RowNotFound
	 */
	public function findUniqueBy(Condition|array $conditions): ?TestRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: false, unique: true);
		\assert($row instanceof TestRow || $row === null);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return TestRow
	 * @throws RowNotFound
	 */
	public function getFirstBy(Condition|array $conditions, array $orderBy = []): TestRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: true, unique: false);
		\assert($row instanceof TestRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return TestRow|null
	 */
	public function findFirstBy(Condition|array $conditions, array $orderBy = []): ?TestRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: false, unique: false);
		\assert($row instanceof TestRow || $row === null);
		return $row;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(TestModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
	}


	public function new(Uuid $id, int $score, string|DefaultValue|null $details = DefaultValue): TestModifications
	{
		$modifications = TestModifications::new();
		$modifications->id = $id;
		$modifications->score = $score;
		if (!$details instanceof DefaultValue) {
			$modifications->details = $details;
		}
		return $modifications;
	}


	public function edit(
		TestRow|TestPrimaryKey $rowOrKey,
		Uuid|UnchangedValue $id = Unchanged,
		int|UnchangedValue $score = Unchanged,
		string|UnchangedValue|DefaultValue|null $details = Unchanged,
	): TestModifications
	{
		$primaryKey = $rowOrKey instanceof TestPrimaryKey ? $rowOrKey : TestPrimaryKey::fromRow($rowOrKey);
		$modifications = TestModifications::update($primaryKey);
		if (!$id instanceof UnchangedValue) {
			$modifications->id = $id;
		}
		if (!$score instanceof UnchangedValue) {
			$modifications->score = $score;
		}
		if (!$details instanceof UnchangedValue) {
			$modifications->details = $details;
		}
		return $modifications;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insert(Uuid $id, int $score, string|DefaultValue|null $details = DefaultValue): void
	{
		$modifications = TestModifications::new();
		$modifications->id = $id;
		$modifications->score = $score;
		if (!$details instanceof DefaultValue) {
			$modifications->details = $details;
		}
		$this->tableManager->insert($this, $modifications);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insertAndGet(Uuid $id, int $score, string|DefaultValue|null $details = DefaultValue): TestRow
	{
		$modifications = TestModifications::new();
		$modifications->id = $id;
		$modifications->score = $score;
		if (!$details instanceof DefaultValue) {
			$modifications->details = $details;
		}
		$row = $this->tableManager->insertAndGet($this, $modifications);
		\assert($row instanceof TestRow);
		return $row;
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function update(
		TestRow|TestPrimaryKey $rowOrKey,
		Uuid|UnchangedValue $id = Unchanged,
		int|UnchangedValue $score = Unchanged,
		string|UnchangedValue|DefaultValue|null $details = Unchanged,
	): void
	{
		$primaryKey = $rowOrKey instanceof TestPrimaryKey ? $rowOrKey : TestPrimaryKey::fromRow($rowOrKey);
		$modifications = TestModifications::update($primaryKey);
		if (!$id instanceof UnchangedValue) {
			$modifications->id = $id;
		}
		if (!$score instanceof UnchangedValue) {
			$modifications->score = $score;
		}
		if (!$details instanceof UnchangedValue) {
			$modifications->details = $details;
		}
		$this->tableManager->update($this, $modifications);
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function updateAndGet(
		TestRow|TestPrimaryKey $rowOrKey,
		Uuid|UnchangedValue $id = Unchanged,
		int|UnchangedValue $score = Unchanged,
		string|UnchangedValue|DefaultValue|null $details = Unchanged,
	): TestRow
	{
		$primaryKey = $rowOrKey instanceof TestPrimaryKey ? $rowOrKey : TestPrimaryKey::fromRow($rowOrKey);
		$modifications = TestModifications::update($primaryKey);
		if (!$id instanceof UnchangedValue) {
			$modifications->id = $id;
		}
		if (!$score instanceof UnchangedValue) {
			$modifications->score = $score;
		}
		if (!$details instanceof UnchangedValue) {
			$modifications->details = $details;
		}
		$row = $this->tableManager->updateAndGet($this, $modifications);
		\assert($row instanceof TestRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 */
	public function updateBy(
		Condition|array $conditions,
		Uuid|UnchangedValue $id = Unchanged,
		int|UnchangedValue $score = Unchanged,
		string|UnchangedValue|DefaultValue|null $details = Unchanged,
	): void
	{
		$modifications = TestModifications::new();
		if (!$id instanceof UnchangedValue) {
			$modifications->id = $id;
		}
		if (!$score instanceof UnchangedValue) {
			$modifications->score = $score;
		}
		if (!$details instanceof UnchangedValue) {
			$modifications->details = $details;
		}
		$this->tableManager->updateBy($this, $conditions, $modifications);
	}


	public function upsert(Uuid $id, int $score, string|DefaultValue|null $details = DefaultValue): void
	{
		$modifications = TestModifications::new();
		$modifications->id = $id;
		$modifications->score = $score;
		if (!$details instanceof DefaultValue) {
			$modifications->details = $details;
		}
		$this->tableManager->upsert($this, $modifications);
	}


	public function upsertAndGet(Uuid $id, int $score, string|DefaultValue|null $details = DefaultValue): TestRow
	{
		$modifications = TestModifications::new();
		$modifications->id = $id;
		$modifications->score = $score;
		if (!$details instanceof DefaultValue) {
			$modifications->details = $details;
		}
		$row = $this->tableManager->upsertAndGet($this, $modifications);
		\assert($row instanceof TestRow);
		return $row;
	}


	public function delete(TestRow|TestPrimaryKey $rowOrKey): void
	{
		$primaryKey = $rowOrKey instanceof TestPrimaryKey ? $rowOrKey : TestPrimaryKey::fromRow($rowOrKey);
		$this->tableManager->delete($this, $primaryKey);
	}


	public function deleteAndGet(TestRow|TestPrimaryKey $rowOrKey): TestRow
	{
		$primaryKey = $rowOrKey instanceof TestPrimaryKey ? $rowOrKey : TestPrimaryKey::fromRow($rowOrKey);
		$row = $this->tableManager->deleteAndGet($this, $primaryKey);
		\assert($row instanceof TestRow);
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
		/** @var Column<self, int> $score */
		$score = Column::from($this, self::getDatabaseColumns()['score'], $this->typeResolver);
		/** @var Column<self, string|null> $details */
		$details = Column::from($this, self::getDatabaseColumns()['details'], $this->typeResolver);
		$this->columns = ['id' => $id, 'score' => $score, 'details' => $details];
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
	#[\Deprecated('Use $score property instead')]
	public function score(): Column
	{
		return $this->columns['score'];
	}


	/**
	 * @return Column<self, string|null>
	 */
	#[\Deprecated('Use $details property instead')]
	public function details(): Column
	{
		return $this->columns['details'];
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
