<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Dibi\IConnection;

interface TableFactory
{
	public function create(): Table;
	public function withTableManager(TableManager $tableManager): Table;
	public function withConnection(IConnection $connection): Table;
}
