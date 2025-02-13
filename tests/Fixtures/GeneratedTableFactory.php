<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\IConnection;
use Grifart\Tables\SingleConnectionTableManager;
use Grifart\Tables\TableManager;
use Grifart\Tables\TypeResolver;

final readonly class GeneratedTableFactory implements \Grifart\Tables\TableFactory
{
	public function __construct(
		private TableManager $tableManager,
		private TypeResolver $typeResolver,
	) {
	}


	public function create(): GeneratedTable
	{
		return new GeneratedTable($this->tableManager, $this->typeResolver);
	}


	public function withTableManager(TableManager $tableManager): GeneratedTable
	{
		return new GeneratedTable($tableManager, $this->typeResolver);
	}


	public function withConnection(IConnection $connection): GeneratedTable
	{
		$tableManager = new SingleConnectionTableManager($connection);
		return new GeneratedTable($tableManager, $this->typeResolver);
	}
}
