<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Grifart\Tables\Tests\Fixtures\GeneratedTable;
use Grifart\Tables\Tests\Fixtures\TestFixtures;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$connection = connect();

$connection->nativeQuery("TRUNCATE TABLE public.generated");

$table = new GeneratedTable(
	TestFixtures::createTableManager($connection),
	TestFixtures::createTypeResolver($connection),
);

$table->insert(42);

$all = $table->getAll();
Assert::count(1, $all);

[$row] = $all;
Assert::type('int', $row->id);
Assert::same($row->id * 2, $row->double);
Assert::same(42, $row->direct);
