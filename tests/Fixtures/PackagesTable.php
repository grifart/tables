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

final class PackagesTable implements Table
{
	public const string NAME = 'name';
	public const string VERSION = 'version';
	public const string PREVIOUS_VERSIONS = 'previousVersions';

	/** @var array{name: Column<self, string>, version: Column<self, array{int, int, int}>, previousVersions: Column<self, Version[]>} */
	private array $columns;

	/** @var Column<self, string> */
	public Column $name {
		get => $this->columns['name'];
	}

	/** @var Column<self, array{int, int, int}> */
	public Column $version {
		get => $this->columns['version'];
	}

	/** @var Column<self, Version[]> */
	public Column $previousVersions {
		get => $this->columns['previousVersions'];
	}


	public static function getSchema(): string
	{
		return 'public';
	}


	public static function getTableName(): string
	{
		return 'package';
	}


	public static function getPrimaryKeyClass(): string
	{
		return PackagePrimaryKey::class;
	}


	public static function getRowClass(): string
	{
		return PackageRow::class;
	}


	public static function getModificationClass(): string
	{
		return PackageModifications::class;
	}


	/**
	 * @return ColumnMetadata[]
	 */
	public static function getDatabaseColumns(): array
	{
		return [
			'name' => new ColumnMetadata('name', 'text', false, false, false),
			'version' => new ColumnMetadata('version', '"packageVersion"', false, false, false),
			'previousVersions' => new ColumnMetadata('previousVersions', '"packageVersion"[]', false, false, false)
		];
	}


	public function find(PackagePrimaryKey $primaryKey): ?PackageRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: false);
		\assert($row instanceof PackageRow || $row === null);
		return $row;
	}


	/**
	 * @throws RowNotFound
	 */
	public function get(PackagePrimaryKey $primaryKey): PackageRow
	{
		$row = $this->tableManager->find($this, $primaryKey, required: true);
		\assert($row instanceof PackageRow);
		return $row;
	}


	/**
	 * @param OrderBy[] $orderBy
	 * @return PackageRow[]
	 */
	public function getAll(array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var PackageRow[] $result */
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
	 * @return PackageRow[]
	 */
	public function findBy(Condition|array $conditions, array $orderBy = [], ?Paginator $paginator = null): array
	{
		/** @var PackageRow[] $result */
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
	 * @return PackageRow
	 * @throws RowNotFound
	 */
	public function getUniqueBy(Condition|array $conditions): PackageRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: true, unique: true);
		\assert($row instanceof PackageRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @return PackageRow|null
	 * @throws RowNotFound
	 */
	public function findUniqueBy(Condition|array $conditions): ?PackageRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, required: false, unique: true);
		\assert($row instanceof PackageRow || $row === null);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return PackageRow
	 * @throws RowNotFound
	 */
	public function getFirstBy(Condition|array $conditions, array $orderBy = []): PackageRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: true, unique: false);
		\assert($row instanceof PackageRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array<OrderBy|Expression<mixed>> $orderBy
	 * @return PackageRow|null
	 */
	public function findFirstBy(Condition|array $conditions, array $orderBy = []): ?PackageRow
	{
		$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: false, unique: false);
		\assert($row instanceof PackageRow || $row === null);
		return $row;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(PackageModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
	}


	/**
	 * @param array{int, int, int} $version
	 * @param Version[] $previousVersions
	 */
	public function new(string $name, array $version, array $previousVersions): PackageModifications
	{
		$modifications = PackageModifications::new();
		$modifications->modifyName($name);
		$modifications->modifyVersion($version);
		$modifications->modifyPreviousVersions($previousVersions);
		return $modifications;
	}


	/**
	 * @param array{int, int, int}|DefaultOrExistingValue $version
	 * @param Version[]|DefaultOrExistingValue $previousVersions
	 */
	public function edit(
		PackageRow|PackagePrimaryKey $rowOrKey,
		string|DefaultOrExistingValue $name = \Grifart\Tables\Unchanged,
		array|DefaultOrExistingValue $version = \Grifart\Tables\Unchanged,
		array|DefaultOrExistingValue $previousVersions = \Grifart\Tables\Unchanged,
	): PackageModifications
	{
		$primaryKey = $rowOrKey instanceof PackagePrimaryKey ? $rowOrKey : PackagePrimaryKey::fromRow($rowOrKey);
		$modifications = PackageModifications::update($primaryKey);
		if (!$name instanceof DefaultOrExistingValue) {
			$modifications->modifyName($name);
		}
		if (!$version instanceof DefaultOrExistingValue) {
			$modifications->modifyVersion($version);
		}
		if (!$previousVersions instanceof DefaultOrExistingValue) {
			$modifications->modifyPreviousVersions($previousVersions);
		}
		return $modifications;
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @param array{int, int, int} $version
	 * @param Version[] $previousVersions
	 */
	public function insert(string $name, array $version, array $previousVersions): void
	{
		$modifications = PackageModifications::new();
		$modifications->modifyName($name);
		$modifications->modifyVersion($version);
		$modifications->modifyPreviousVersions($previousVersions);
		$this->tableManager->insert($this, $modifications);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 * @param array{int, int, int} $version
	 * @param Version[] $previousVersions
	 */
	public function insertAndGet(string $name, array $version, array $previousVersions): PackageRow
	{
		$modifications = PackageModifications::new();
		$modifications->modifyName($name);
		$modifications->modifyVersion($version);
		$modifications->modifyPreviousVersions($previousVersions);
		$row = $this->tableManager->insertAndGet($this, $modifications);
		\assert($row instanceof PackageRow);
		return $row;
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 * @param array{int, int, int}|DefaultOrExistingValue $version
	 * @param Version[]|DefaultOrExistingValue $previousVersions
	 */
	public function update(
		PackageRow|PackagePrimaryKey $rowOrKey,
		string|DefaultOrExistingValue $name = \Grifart\Tables\Unchanged,
		array|DefaultOrExistingValue $version = \Grifart\Tables\Unchanged,
		array|DefaultOrExistingValue $previousVersions = \Grifart\Tables\Unchanged,
	): void
	{
		$primaryKey = $rowOrKey instanceof PackagePrimaryKey ? $rowOrKey : PackagePrimaryKey::fromRow($rowOrKey);
		$modifications = PackageModifications::update($primaryKey);
		if (!$name instanceof DefaultOrExistingValue) {
			$modifications->modifyName($name);
		}
		if (!$version instanceof DefaultOrExistingValue) {
			$modifications->modifyVersion($version);
		}
		if (!$previousVersions instanceof DefaultOrExistingValue) {
			$modifications->modifyPreviousVersions($previousVersions);
		}
		$this->tableManager->update($this, $modifications);
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 * @param array{int, int, int}|DefaultOrExistingValue $version
	 * @param Version[]|DefaultOrExistingValue $previousVersions
	 */
	public function updateAndGet(
		PackageRow|PackagePrimaryKey $rowOrKey,
		string|DefaultOrExistingValue $name = \Grifart\Tables\Unchanged,
		array|DefaultOrExistingValue $version = \Grifart\Tables\Unchanged,
		array|DefaultOrExistingValue $previousVersions = \Grifart\Tables\Unchanged,
	): PackageRow
	{
		$primaryKey = $rowOrKey instanceof PackagePrimaryKey ? $rowOrKey : PackagePrimaryKey::fromRow($rowOrKey);
		$modifications = PackageModifications::update($primaryKey);
		if (!$name instanceof DefaultOrExistingValue) {
			$modifications->modifyName($name);
		}
		if (!$version instanceof DefaultOrExistingValue) {
			$modifications->modifyVersion($version);
		}
		if (!$previousVersions instanceof DefaultOrExistingValue) {
			$modifications->modifyPreviousVersions($previousVersions);
		}
		$row = $this->tableManager->updateAndGet($this, $modifications);
		\assert($row instanceof PackageRow);
		return $row;
	}


	/**
	 * @param Condition|Condition[] $conditions
	 * @param array{int, int, int}|DefaultOrExistingValue $version
	 * @param Version[]|DefaultOrExistingValue $previousVersions
	 */
	public function updateBy(
		Condition|array $conditions,
		string|DefaultOrExistingValue $name = \Grifart\Tables\Unchanged,
		array|DefaultOrExistingValue $version = \Grifart\Tables\Unchanged,
		array|DefaultOrExistingValue $previousVersions = \Grifart\Tables\Unchanged,
	): void
	{
		$modifications = PackageModifications::new();
		if (!$name instanceof DefaultOrExistingValue) {
			$modifications->modifyName($name);
		}
		if (!$version instanceof DefaultOrExistingValue) {
			$modifications->modifyVersion($version);
		}
		if (!$previousVersions instanceof DefaultOrExistingValue) {
			$modifications->modifyPreviousVersions($previousVersions);
		}
		$this->tableManager->updateBy($this, $conditions, $modifications);
	}


	/**
	 * @param array{int, int, int} $version
	 * @param Version[] $previousVersions
	 */
	public function upsert(string $name, array $version, array $previousVersions): void
	{
		$modifications = PackageModifications::new();
		$modifications->modifyName($name);
		$modifications->modifyVersion($version);
		$modifications->modifyPreviousVersions($previousVersions);
		$this->tableManager->upsert($this, $modifications);
	}


	/**
	 * @param array{int, int, int} $version
	 * @param Version[] $previousVersions
	 */
	public function upsertAndGet(string $name, array $version, array $previousVersions): PackageRow
	{
		$modifications = PackageModifications::new();
		$modifications->modifyName($name);
		$modifications->modifyVersion($version);
		$modifications->modifyPreviousVersions($previousVersions);
		$row = $this->tableManager->upsertAndGet($this, $modifications);
		\assert($row instanceof PackageRow);
		return $row;
	}


	public function delete(PackageRow|PackagePrimaryKey $rowOrKey): void
	{
		$primaryKey = $rowOrKey instanceof PackagePrimaryKey ? $rowOrKey : PackagePrimaryKey::fromRow($rowOrKey);
		$this->tableManager->delete($this, $primaryKey);
	}


	public function deleteAndGet(PackageRow|PackagePrimaryKey $rowOrKey): PackageRow
	{
		$primaryKey = $rowOrKey instanceof PackagePrimaryKey ? $rowOrKey : PackagePrimaryKey::fromRow($rowOrKey);
		$row = $this->tableManager->deleteAndGet($this, $primaryKey);
		\assert($row instanceof PackageRow);
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
		/** @var Column<self, string> $name */
		$name = Column::from($this, self::getDatabaseColumns()['name'], $this->typeResolver);
		/** @var Column<self, array{int, int, int}> $version */
		$version = Column::from($this, self::getDatabaseColumns()['version'], $this->typeResolver);
		/** @var Column<self, Version[]> $previousVersions */
		$previousVersions = Column::from($this, self::getDatabaseColumns()['previousVersions'], $this->typeResolver);
		$this->columns = ['name' => $name, 'version' => $version, 'previousVersions' => $previousVersions];
	}


	/**
	 * @return Column<self, string>
	 */
	#[\Deprecated('Use $name property instead')]
	public function name(): Column
	{
		return $this->columns['name'];
	}


	/**
	 * @return Column<self, array{int, int, int}>
	 */
	#[\Deprecated('Use $version property instead')]
	public function version(): Column
	{
		return $this->columns['version'];
	}


	/**
	 * @return Column<self, Version[]>
	 */
	#[\Deprecated('Use $previousVersions property instead')]
	public function previousVersions(): Column
	{
		return $this->columns['previousVersions'];
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
