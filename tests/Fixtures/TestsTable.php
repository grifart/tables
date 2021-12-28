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
use Grifart\Tables\RowNotFound;
use Grifart\Tables\Table;
use Grifart\Tables\TableManager;
use Grifart\Tables\Type;
use Grifart\Tables\TypeResolver;

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
			'id' => new ColumnMetadata('id', 'uuid', false, false),
			'score' => new ColumnMetadata('score', 'integer', false, false),
			'details' => new ColumnMetadata('details', 'character varying', true, true)
		];
	}


	public function find(TestPrimaryKey $primaryKey): ?TestRow
	{
		$row = $this->tableManager->find($this, $primaryKey);
		\assert($row instanceof TestRow || $row === NULL);
		return $row;
	}


	/**
	 * @throws RowNotFound
	 */
	public function get(TestPrimaryKey $primaryKey): TestRow
	{
		$row = $this->find($primaryKey);
		if ($row === NULL) {
			throw new RowNotFound();
		}
		return $row;
	}


	/**
	 * @return TestRow[]
	 */
	public function getAll(): array
	{
		/** @var TestRow[] $result */
		$result = $this->tableManager->getAll($this);
		return $result;
	}


	/**
	 * @param Condition<mixed>[] $conditions
	 * @return TestRow[]
	 */
	public function findBy(array $conditions): array
	{
		/** @var TestRow[] $result */
		$result = $this->tableManager->findBy($this, $conditions);
		return $result;
	}


	public function newEmpty(): TestModifications
	{
		return TestModifications::new();
	}


	public function new(Uuid $id, int $score): TestModifications
	{
		$modifications = TestModifications::new();
		$modifications->modifyId($id);
		$modifications->modifyScore($score);
		return $modifications;
	}


	public function edit(TestRow $row): TestModifications
	{
		/** @var TestPrimaryKey $primaryKeyClass */
		$primaryKeyClass = self::getPrimaryKeyClass();

		return TestModifications::update(
			$primaryKeyClass::fromRow($row)
		);
	}


	public function editByKey(TestPrimaryKey $primaryKey): TestModifications
	{
		return TestModifications::update($primaryKey);
	}


	public function save(TestModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
	}


	public function delete(TestPrimaryKey $primaryKey): void
	{
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
