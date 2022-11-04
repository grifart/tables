<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Grifart\Tables\Tests\Fixtures\PackagesTable;
use Grifart\Tables\Tests\Fixtures\TestFixtures;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$connection = connect();

$connection->nativeQuery("TRUNCATE TABLE public.package");

$table = new PackagesTable(
	TestFixtures::createTableManager($connection),
	TestFixtures::createTypeResolver(),
);

$package = $table->new('grifart/tables', [0, 8, 0]);
$table->save($package);

$byVersion = $table->findBy([
	$table->version()->is([0, 8, 0]),
]);
Assert::count(1, $byVersion);

[$row] = $byVersion;
Assert::equal([0, 8, 0], $row->getVersion());
