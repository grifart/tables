<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Connection;
use Grifart\Tables\Scaffolding\PostgresReflector;
use Grifart\Tables\Scaffolding\Scaffolding;

$connection = require __DIR__ . '/../createConnection.local.php';
\assert($connection instanceof Connection);

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
