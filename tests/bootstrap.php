<?php

declare(strict_types = 1);

namespace Grifart\Tables\Tests;

use Dibi\Connection;
use Tester\Environment;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();

function connect(): Connection {
	$connection = require __DIR__ . '/createConnection.local.php';
	\assert($connection instanceof Connection);

	return $connection;
}
