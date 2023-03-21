<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Connection;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Scaffolding\PostgresReflector;
use Grifart\Tables\Scaffolding\TablesDefinitions;
use Grifart\Tables\TypeResolver;
use Grifart\Tables\Types\TextType;


$connection = require __DIR__ . '/../createConnection.local.php';
\assert($connection instanceof Connection);

$reflector = new PostgresReflector($connection);
$typeResolver = (new TypeResolver($connection));
$typeResolver->addResolutionByLocation(new Identifier('public', 'missingPrimaryIndex', 'whatever'), TextType::text());
$tableDefinitions = new TablesDefinitions($reflector, $typeResolver);

return [
	...$tableDefinitions->for(
		'public',
		'missingPrimaryIndex',
		MissingPrimaryIndexRow::class,
		MissingPrimaryIndexModifications::class,
		MissingPrimaryIndexTable::class,
		MissingPrimaryIndexPrimaryKey::class,
	)
];
