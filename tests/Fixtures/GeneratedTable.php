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

final class GeneratedTable implements Table
{
	public const ID = 'id';
	public const DOUBLE = 'double';
	public const DIRECT = 'direct';

	/** @var array{id: Column<self, int>, double: Column<self, int>, direct: Column<self, int>} */
	private array $columns;


	public static function getSchema(): string
	{
		return 'public';
	}


	public static function getTableName(): string
	{
		return 'generated';
	}


	public static function getPrimaryKeyClass(): string
	{
		return GeneratedPrimaryKey::class;
	}


	public static function getRowClass(): string
	{
		return GeneratedRow::class;
	}


	public static function getModificationClass(): string
	{
		return GeneratedModifications::class;
	}


	/**
	 * @return ColumnMetadata[]
	 */
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
	 * @param Condition|Condition[] $conditions
	 * @return GeneratedRow
	 * @throws RowNotFound
	 */
	#[\Deprecated('Use getUniqueBy() instead.')]
	public function getBy(Condition|array $conditions): GeneratedRow
	{
		return $this->getUniqueBy($conditions);
	}


	public function new(int $direct): GeneratedModifications
	{
		$modifications = GeneratedModifications::new();
		$modifications->modifyDirect($direct);
		return $modifications;
	}


	public function edit(
		GeneratedRow|GeneratedPrimaryKey $rowOrKey,
		int|DefaultOrExistingValue $direct = \Grifart\Tables\Unchanged,
	): GeneratedModifications
	{
		$primaryKey = $rowOrKey instanceof GeneratedPrimaryKey ? $rowOrKey : GeneratedPrimaryKey::fromRow($rowOrKey);
		$modifications = GeneratedModifications::update($primaryKey);
		if (!$direct instanceof DefaultOrExistingValue) {
			$modifications->modifyDirect($direct);
		}
		return $modifications;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(GeneratedModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insert(GeneratedModifications $changes): void
	{
		$this->tableManager->insert($this, $changes);
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function update(GeneratedModifications $changes): void
	{
		$this->tableManager->update($this, $changes);
	}


	/**
	 * @param Condition|Condition[] $conditions
	 */
	public function updateBy(Condition|array $conditions, GeneratedModifications $changes): void
	{
		$this->tableManager->updateBy($this, $conditions, $changes);
	}


	public function delete(GeneratedRow|GeneratedPrimaryKey $rowOrKey): void
	{
		$primaryKey = $rowOrKey instanceof GeneratedPrimaryKey ? $rowOrKey : GeneratedPrimaryKey::fromRow($rowOrKey);
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
	public function id(): Column
	{
		return $this->columns['id'];
	}


	/**
	 * @return Column<self, int>
	 */
	public function double(): Column
	{
		return $this->columns['double'];
	}


	/**
	 * @return Column<self, int>
	 */
	public function direct(): Column
	{
		return $this->columns['direct'];
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
