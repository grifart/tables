<?php declare(strict_types=1);


namespace Grifart\Tables;


trait ModificationsTrait //implements Changes
{

	/** @var mixed[] */
	protected $modifications;

	/** @var PrimaryKey|null */
	private $primaryKey;


	private function __construct()
	{
		$this->modifications = [];
	}


	// PHP does not support parameter specialization
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


	public function getPrimaryKey(): ?PrimaryKey
	{
		return $this->primaryKey;
	}

}
