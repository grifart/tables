<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Connection;
use Grifart\Tables\Scaffolding\PostgresReflector;
use Grifart\Tables\Scaffolding\Scaffolding;

$connection = require __DIR__ . '/../createConnection.local.php';
\assert($connection instanceof Connection);

$connection->nativeQuery(<<<SQL
CREATE TABLE IF NOT EXISTS public.test (
    id uuid NOT NULL PRIMARY KEY,
    score int NOT NULL,
    details varchar DEFAULT NULL
);
SQL);

$reflector = new PostgresReflector($connection);
$typeResolver = TestFixtures::createTypeResolver();

return Scaffolding::definitionsForPgTable(
	$reflector,
	$typeResolver,
	'public',
	'test',
	TestRow::class,
	TestModifications::class,
	TestsTable::class,
	TestPrimaryKey::class,
);
