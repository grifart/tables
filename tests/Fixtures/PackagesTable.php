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
	public const NAME = 'name';
	public const VERSION = 'version';
	public const PREVIOUS_VERSIONS = 'previousVersions';

	/** @var array{name: Column<self, string>, version: Column<self, array{int, int, int}>, previousVersions: Column<self, Version[]>} */
	private array $columns;


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
		$row = $this->tableManager->find($this, $primaryKey);
		\assert($row instanceof PackageRow || $row === NULL);
		return $row;
	}


	/**
	 * @throws RowNotFound
	 */
	public function get(PackagePrimaryKey $primaryKey): PackageRow
	{
		$row = $this->find($primaryKey);
		if ($row === NULL) {
			throw new RowNotFound();
		}
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
	 * @return PackageRow
	 * @throws RowNotFound
	 */
	public function getBy(Condition|array $conditions): PackageRow
	{
		$result = $this->findBy($conditions);
		if (\count($result) === 0) { throw new RowNotFound(); }
		if (\count($result) > 1) { throw new TooManyRowsFound(); }
		return $result[0];
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
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function save(PackageModifications $changes): void
	{
		$this->tableManager->save($this, $changes);
	}


	/**
	 * @throws RowWithGivenPrimaryKeyAlreadyExists
	 */
	public function insert(PackageModifications $changes): void
	{
		$this->tableManager->insert($this, $changes);
	}


	/**
	 * @throws GivenSearchCriteriaHaveNotMatchedAnyRows
	 */
	public function update(PackageModifications $changes): void
	{
		$this->tableManager->update($this, $changes);
	}


	public function delete(PackageRow|PackagePrimaryKey $rowOrKey): void
	{
		$primaryKey = $rowOrKey instanceof PackagePrimaryKey ? $rowOrKey : PackagePrimaryKey::fromRow($rowOrKey);
		$this->tableManager->delete($this, $primaryKey);
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
	public function name(): Column
	{
		return $this->columns['name'];
	}


	/**
	 * @return Column<self, array{int, int, int}>
	 */
	public function version(): Column
	{
		return $this->columns['version'];
	}


	/**
	 * @return Column<self, Version[]>
	 */
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
