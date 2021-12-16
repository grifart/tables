<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Dibi\Connection;

require __DIR__ . '/../vendor/autoload.php';

$connection = require __DIR__ . '/createConnection.local.php';
\assert($connection instanceof Connection);

$connection->nativeQuery(<<<SQL
CREATE TABLE IF NOT EXISTS public.test (
    id uuid NOT NULL PRIMARY KEY,
    score int NOT NULL,
    details varchar DEFAULT NULL
);
SQL);
