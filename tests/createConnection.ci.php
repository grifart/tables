<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Dibi\Connection;

return new Connection([
	'driver' => 'postgre',
	'host' => 'postgres',
	'username' => 'postgres',
	'password' => 'postgres',
	'dbname' => 'tables',
]);
