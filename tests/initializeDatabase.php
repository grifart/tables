<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Dibi\Connection;

require __DIR__ . '/../vendor/autoload.php';

$connection = require __DIR__ . '/createConnection.local.php';
\assert($connection instanceof Connection);

if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] === '--reset') {
	$connection->nativeQuery('DROP SCHEMA IF EXISTS public CASCADE');
}

$connection->nativeQuery('CREATE SCHEMA IF NOT EXISTS public');

$connection->nativeQuery(<<<SQL
CREATE TABLE IF NOT EXISTS public.test (
    id uuid NOT NULL PRIMARY KEY,
    score int NOT NULL,
    details varchar DEFAULT NULL
);
SQL);

$connection->nativeQuery(<<<SQL
CREATE TABLE IF NOT EXISTS public.config (
    id uuid NOT NULL PRIMARY KEY,
    key text NOT NULL UNIQUE,
    value text NOT NULL
);
SQL);

$connection->nativeQuery(<<<SQL
CREATE TYPE public."packageVersion" AS (major int, minor int, patch int);
CREATE TABLE IF NOT EXISTS public.package (
    name text NOT NULL PRIMARY KEY,
    version public."packageVersion" NOT NULL,
    "previousVersions" public."packageVersion"[] NOT NULL
);
SQL);

$connection->nativeQuery(<<<SQL
CREATE TABLE IF NOT EXISTS public."missingPrimaryIndex" (
    whatever text NOT NULL
);
SQL);

$connection->nativeQuery(<<<SQL
CREATE TABLE IF NOT EXISTS public.generated (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    double INT NOT NULL GENERATED ALWAYS AS (id * 2) STORED,
    direct INT NOT NULL
)
SQL);

$connection->nativeQuery(<<<SQL
CREATE TABLE IF NOT EXISTS public.bulk (
    id uuid NOT NULL PRIMARY KEY,
    value int NOT NULL,
    flagged boolean NOT NULL DEFAULT false
)
SQL);
