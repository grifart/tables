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

final class ConfigTable implements Table
{
	public const ID = 'id';
	public const KEY = 'key';
	public const VALUE = 'value';

	/** @var array{id: Column<self, Uuid>, key: Column<self, string>, value: Column<self, string>} */
	private array $columns;


	public static function getSchema(): string
	{
		return 'public';
	}


	public static function getTableName(): string
	{
		return 'config';
	}


	public static function getPrimaryKeyClass(): string
	{
		return ConfigPrimaryKey::class;
	}


	public static function getRowClass(): string
	{
		return ConfigRow::class;
	}


	public static function getModificationClass(): string
	{
		return ConfigModifications::class;
	}


	/**
	 * @return ColumnMetadata[]
	 */
	public static function getDatabaseColumns(): array
	{
		return [
			'id' => new ColumnMetadata('id', 'uuid', false, false, false),
			'key' => new ColumnMetadata('key', 'text', false, false, false),
			'value' => new ColumnMetadata('value', 'text', false, false, false)
		];
	}


	public function find(ConfigPrimaryKey $primaryKey): ?ConfigRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: false);
		\assert($row instanceof ConfigRow || $row === null);
		return $row;
	}


	/**
	 * @throws RowNotFound
	 */
	public function get(ConfigPrimaryKey $primaryKey): ConfigRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: true);
		\assert($row instanceof ConfigRow);
		return $row;
	}


	/**
	 * @param OrderBy[] $orderBy
	 * @return ConfigRow[]
	 */
	public function getAll(array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var ConfigRow[] $result */
		$result = $this->tableManager->getAll($this, $orderBy, $paginator);
		return $result;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return ConfigRow[]
	 */
	public function findBy(Condition|array $conditions, array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var ConfigRow[] $result */
		$result = $this->tableManager->findBy($this, $conditions, $orderBy, $paginator);
		return $result;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return ConfigRow
	 * @throws RowNotFound
	 */
	public function getUniqueBy(Condition|array $conditions): ConfigRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: true, unique: true);
		\assert($row instanceof ConfigRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return ConfigRow|null
	 * @throws RowNotFound
	 */
	public function findUniqueBy(Condition|array $conditions): ?ConfigRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: false, unique: true);
		\assert($row instanceof ConfigRow || $row === null);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return ConfigRow
	 * @throws RowNotFound
	 */
	public function getFirstBy(Condition|array $conditions, array $orderBy = []): ConfigRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: true, unique: false);
		\assert($row instanceof ConfigRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return ConfigRow|null
	 */
	public function findFirstBy(Condition|array $conditions, array $orderBy = []): ?ConfigRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: false, unique: false);
		\assert($row instanceof ConfigRow || $row === null);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return ConfigRow
	 * @throws RowNotFound
	 */
	#[\Deprecated('Use getUniqueBy() instead.')]
	public function getBy(Condition|array $conditions): ConfigRow
	{
		return $this->getUniqueBy($conditions);
	}


	public function new(Uuid $id, string $key, string $value): ConfigModifications
	{
		$modifications = ConfigModifications::new();
		$modifications->modifyId($id);
		$modifications->modifyKey($key);
		$modifications->modifyValue($value);
		return $modifications;
	}


	public function edit(
		ConfigRow|ConfigPrimaryKey $rowOrKey,
		Uuid|DefaultOrExistingValue $id = \Grifart\Tables\Unchanged,
		string|DefaultOrExistingValue $key = \Grifart\Tables\Unchanged,
		string|DefaultOrExistingValue $value = \Grifart\Tables\Unchanged,
	): ConfigModifications
	{
		$primaryKey = $rowOrKey instanceof ConfigPrimaryKey ? $rowOrKey : ConfigPrimaryKey::fromRow($rowOrKey);
		$modifications = ConfigModifications::update($primaryKey);
		if (!$id instanceof DefaultOrExistingValue) {
			$modifications->modifyId($id);
		}
		if (!$key instanceof DefaultOrExistingValue) {
			$modifications->modifyKey($key);
		}
		if (!$value instanceof DefaultOrExistingValue) {
			$modifications->modifyValue($value);
		}
		return $modifications;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(ConfigModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insert(ConfigModifications $changes): void
	{
		$this->tableManager->insert($this, $changes);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insertAndGet(ConfigModifications $changes): ConfigRow
	{
		$row = $this->tableManager->insertAndGet($this, $changes);
		\assert($row instanceof ConfigRow);
		return $row;
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function update(ConfigModifications $changes): void
	{
		$this->tableManager->update($this, $changes);
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function updateAndGet(ConfigModifications $changes): ConfigRow
	{
		$row = $this->tableManager->updateAndGet($this, $changes);
		\assert($row instanceof ConfigRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 */
	public function updateBy(Condition|array $conditions, ConfigModifications $changes): void
	{
		$this->tableManager->updateBy($this, $conditions, $changes);
	}


	public function upsert(ConfigModifications $changes): void
	{
		$this->tableManager->upsert($this, $changes);
	}


	public function upsertAndGet(ConfigModifications $changes): ConfigRow
	{
		$row = $this->tableManager->upsertAndGet($this, $changes);
		\assert($row instanceof ConfigRow);
		return $row;
	}


	public function delete(ConfigRow|ConfigPrimaryKey $rowOrKey): void
	{
		$primaryKey = $rowOrKey instanceof ConfigPrimaryKey ? $rowOrKey : ConfigPrimaryKey::fromRow($rowOrKey);
		$this->tableManager->delete($this, $primaryKey);
	}


	public function deleteAndGet(ConfigRow|ConfigPrimaryKey $rowOrKey): ConfigRow
	{
		$primaryKey = $rowOrKey instanceof ConfigPrimaryKey ? $rowOrKey : ConfigPrimaryKey::fromRow($rowOrKey);
		$row = $this->tableManager->deleteAndGet($this, $primaryKey);
		\assert($row instanceof ConfigRow);
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
		/** @var Column<self, string> $key */
		$key = Column::from($this, self::getDatabaseColumns()['key'], $this->typeResolver);
		/** @var Column<self, string> $value */
		$value = Column::from($this, self::getDatabaseColumns()['value'], $this->typeResolver);
		$this->columns = ['id' => $id, 'key' => $key, 'value' => $value];
	}


	/**
	 * @return Column<self, Uuid>
	 */
	public function id(): Column
	{
		return $this->columns['id'];
	}


	/**
	 * @return Column<self, string>
	 */
	public function key(): Column
	{
		return $this->columns['key'];
	}


	/**
	 * @return Column<self, string>
	 */
	public function value(): Column
	{
		return $this->columns['value'];
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
