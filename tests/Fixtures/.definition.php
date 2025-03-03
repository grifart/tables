<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Connection;
use Grifart\Tables\Scaffolding\PostgresReflector;
use Grifart\Tables\Scaffolding\TablesDefinitions;

$connection = require __DIR__ . '/../createConnection.local.php';
\assert($connection instanceof Connection);

$reflector = new PostgresReflector($connection);
$typeResolver = TestFixtures::createTypeResolver($connection);
$tableDefinitions = new TablesDefinitions($reflector, $typeResolver);

return [
	...$tableDefinitions->for(
		'public',
		'test',
		TestRow::class,
		TestModifications::class,
		TestsTable::class,
		TestPrimaryKey::class,
	)->withFactory(),
	...$tableDefinitions->for(
		'public',
		'config',
		ConfigRow::class,
		ConfigModifications::class,
		ConfigTable::class,
		ConfigPrimaryKey::class,
	),
	...$tableDefinitions->for(
		'public',
		'package',
		PackageRow::class,
		PackageModifications::class,
		PackagesTable::class,
		PackagePrimaryKey::class,
	),
	...$tableDefinitions->for(
		'public',
		'generated',
		GeneratedRow::class,
		GeneratedModifications::class,
		GeneratedTable::class,
		GeneratedPrimaryKey::class,
	),
];
