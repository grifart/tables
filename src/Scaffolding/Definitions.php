<?php declare(strict_types = 1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\Definition\ClassDefinition;


/**
 * @implements \IteratorAggregate<ClassDefinition>
 */
final class Definitions implements \IteratorAggregate
{
	private function __construct(
		private ClassDefinition $rowClass,
		private ClassDefinition $modificationsClass,
		private ?ClassDefinition $primaryKeyClass,
		private ClassDefinition $tableClass,
	) {}

	public static function from(
		ClassDefinition $rowClass,
		ClassDefinition $modificationsClass,
		ClassDefinition $primaryKeyClass,
		ClassDefinition $tableClass,
	): self
	{
		return new self(
			$rowClass,
			$modificationsClass,
			$primaryKeyClass,
			$tableClass,
		);
	}

	public function rowClassWith(Capability $capability, Capability ...$capabilities): self
	{
		$this->rowClass = $this->rowClass->with($capability, ...$capabilities);
		return $this;
	}

	public function modificationsClassWith(Capability $capability, Capability ...$capabilities): self
	{
		$this->modificationsClass = $this->modificationsClass->with($capability, ...$capabilities);
		return $this;
	}

	public function primaryKeyClassWith(Capability $capability, Capability ...$capabilities): self
	{
		$this->primaryKeyClass = $this->primaryKeyClass?->with($capability, ...$capabilities);
		return $this;
	}

	public function withoutPrimaryKeyClass(): self
	{
		$this->primaryKeyClass = null;
		return $this;
	}

	public function tableClassWith(Capability $capability, Capability ...$capabilities): self
	{
		$this->tableClass = $this->tableClass->with($capability, ...$capabilities);
		return $this;
	}


	public function getIterator(): \Traversable
	{
		yield $this->rowClass;
		yield $this->modificationsClass;
		yield $this->tableClass;
		if ($this->primaryKeyClass !== null) {
			yield $this->primaryKeyClass;
		}
	}

}
