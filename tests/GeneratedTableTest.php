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

$changeSet = $table->new(42);
$table->insert($changeSet);

$all = $table->getAll();
Assert::count(1, $all);

[$row] = $all;
Assert::type('int', $row->getId());
Assert::same($row->getId() * 2, $row->getDouble());
Assert::same(42, $row->getDirect());
