<?php declare(strict_types = 1);

namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;


/**
 * @implements \IteratorAggregate<ClassDefinition>
 */
final class Builders implements \IteratorAggregate
{
	private function __construct(
		private ClassDefinitionBuilder $rowClass,
		private ClassDefinitionBuilder $modificationsClass,
		private ClassDefinitionBuilder $tableClass,
	) {}

	public static function from(
		ClassDefinitionBuilder $rowClass,
		ClassDefinitionBuilder $modificationsClass,
		ClassDefinitionBuilder $tableClass,
	): self
	{
		return new self(
			$rowClass,
			$modificationsClass,
			$tableClass,
		);
	}

	public function getRowClass(): ClassDefinitionBuilder
	{
		return $this->rowClass;
	}

	public function getModificationsClass(): ClassDefinitionBuilder
	{
		return $this->modificationsClass;
	}

	public function getTableClass(): ClassDefinitionBuilder
	{
		return $this->tableClass;
	}


	public function getIterator(): \Traversable
	{
		yield $this->rowClass->build();
		yield $this->modificationsClass->build();
		yield $this->tableClass->build();
	}

}
