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

final class TestsTable implements Table
{
	public const ID = 'id';
	public const SCORE = 'score';
	public const DETAILS = 'details';

	/** @var array{id: Column<self, Uuid>, score: Column<self, int>, details: Column<self, string|null>} */
	private array $columns;


	public static function getSchema(): string
	{
		return 'public';
	}


	public static function getTableName(): string
	{
		return 'test';
	}


	public static function getPrimaryKeyClass(): string
	{
		return TestPrimaryKey::class;
	}


	public static function getRowClass(): string
	{
		return TestRow::class;
	}


	public static function getModificationClass(): string
	{
		return TestModifications::class;
	}


	/**
	 * @return ColumnMetadata[]
	 */
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
	 * @param Condition|Condition[] $conditions
	 * @return TestRow
	 * @throws RowNotFound
	 */
	#[\Deprecated('Use getUniqueBy() instead.')]
	public function getBy(Condition|array $conditions): TestRow
	{
		return $this->getUniqueBy($conditions);
	}


	public function new(
		Uuid $id,
		int $score,
		string|DefaultOrExistingValue|null $details = \Grifart\Tables\DefaultValue,
	): TestModifications
	{
		$modifications = TestModifications::new();
		$modifications->modifyId($id);
		$modifications->modifyScore($score);
		if (!$details instanceof DefaultOrExistingValue) {
			$modifications->modifyDetails($details);
		}
		return $modifications;
	}


	public function edit(
		TestRow|TestPrimaryKey $rowOrKey,
		Uuid|DefaultOrExistingValue $id = \Grifart\Tables\Unchanged,
		int|DefaultOrExistingValue $score = \Grifart\Tables\Unchanged,
		string|DefaultOrExistingValue|null $details = \Grifart\Tables\Unchanged,
	): TestModifications
	{
		$primaryKey = $rowOrKey instanceof TestPrimaryKey ? $rowOrKey : TestPrimaryKey::fromRow($rowOrKey);
		$modifications = TestModifications::update($primaryKey);
		if (!$id instanceof DefaultOrExistingValue) {
			$modifications->modifyId($id);
		}
		if (!$score instanceof DefaultOrExistingValue) {
			$modifications->modifyScore($score);
		}
		if (!$details instanceof DefaultOrExistingValue) {
			$modifications->modifyDetails($details);
		}
		return $modifications;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(TestModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insert(TestModifications $changes): void
	{
		$this->tableManager->insert($this, $changes);
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function update(TestModifications $changes): void
	{
		$this->tableManager->update($this, $changes);
	}


	public function delete(TestRow|TestPrimaryKey $rowOrKey): void
	{
		$primaryKey = $rowOrKey instanceof TestPrimaryKey ? $rowOrKey : TestPrimaryKey::fromRow($rowOrKey);
		$this->tableManager->delete($this, $primaryKey);
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
	public function id(): Column
	{
		return $this->columns['id'];
	}


	/**
	 * @return Column<self, int>
	 */
	public function score(): Column
	{
		return $this->columns['score'];
	}


	/**
	 * @return Column<self, string|null>
	 */
	public function details(): Column
	{
		return $this->columns['details'];
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
