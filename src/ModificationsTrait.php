<?php declare(strict_types=1);


namespace Grifart\Tables;


/**
 * @template TableType of Table
 */
trait ModificationsTrait //implements Changes
{

	/** @var mixed[] */
	protected array $modifications = [];

	/** @var PrimaryKey<TableType>|null */
	private ?PrimaryKey $primaryKey = null;


	private function __construct()
	{
	}


	/**
	 * @param PrimaryKey<TableType> $primaryKey
	 */
	private static function _update(PrimaryKey $primaryKey): self
	{
		$self = new static();
		$self->primaryKey = $primaryKey;
		return $self;
	}


	// PHP does not support parameter specialization
	private static function _new(): self
	{
		return new static();
	}


	/** @internal used by {@see AccountsTable} */
	public function getModifications(): array {
		return $this->modifications;
	}


	/**
	 * @return PrimaryKey<TableType>|null
	 */
	public function getPrimaryKey(): ?PrimaryKey
	{
		return $this->primaryKey;
	}

}
