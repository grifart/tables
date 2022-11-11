<?php

declare(strict_types = 1);

namespace Grifart\Tables\Tests;

use Dibi\Connection;
use Dibi\Expression;
use Tester\Environment;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();

function connect(): Connection {
	if ( ! \file_exists(__DIR__ . '/createConnection.local.php')) {
		throw new \RuntimeException('Missing connection credentials. Please create tests/createConnection.local.php that returns a configured instance of Dibi\Connection.');
	}

	$connection = require __DIR__ . '/createConnection.local.php';
	\assert($connection instanceof Connection);

	if ($connection->nativeQuery("SELECT to_regclass('public.test');")->fetchSingle() === null) {
		throw new \RuntimeException('Uninitialized database. Please run `php tests/initializeDatabase.php` to initialize the database schema for tests.');
	}

	return $connection;
}

function executeExpressionInDatabase(Connection $connection, Expression $expression): string {
	$result = $connection->query('SELECT ?', $expression)->fetchSingle();
	\assert(is_string($result));
	return $result;
}
