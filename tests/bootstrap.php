<?php

declare(strict_types = 1);

namespace Grifart\Tables\Tests;

use Dibi\Connection;
use Tester\Environment;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();

function connect(): Connection {
	if ( ! \file_exists(__DIR__ . '/createConnection.local.php')) {
		throw new \RuntimeException('Missing connection credentials. Please create tests/createConnection.local.php that returns a configured instance of Dibi\Connection.');
	}

	$connection = require __DIR__ . '/createConnection.local.php';
	\assert($connection instanceof Connection);

	return $connection;
}
