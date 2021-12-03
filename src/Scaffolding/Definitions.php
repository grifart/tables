<?php declare(strict_types = 1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Definition\ClassDefinition;


/**
 * @implements \IteratorAggregate<ClassDefinition>
 */
final class Definitions implements \IteratorAggregate
{
	private function __construct(
		private ClassDefinition $rowClass,
		private ClassDefinition $modificationsClass,
		private ClassDefinition $tableClass,
	) {}

	public static function from(
		ClassDefinition $rowClass,
		ClassDefinition $modificationsClass,
		ClassDefinition $tableClass,
	): self
	{
		return new self(
			$rowClass,
			$modificationsClass,
			$tableClass,
		);
	}

	public function getRowClass(): ClassDefinition
	{
		return $this->rowClass;
	}

	public function getModificationsClass(): ClassDefinition
	{
		return $this->modificationsClass;
	}

	public function getTableClass(): ClassDefinition
	{
		return $this->tableClass;
	}


	public function getIterator(): \Traversable
	{
		yield $this->rowClass;
		yield $this->modificationsClass;
		yield $this->tableClass;
	}

}
