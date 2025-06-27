<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Grifart\Tables\RowNotFound;
use Grifart\Tables\Tests\Fixtures\BulkModifications;
use Grifart\Tables\Tests\Fixtures\BulkTable;
use Grifart\Tables\Tests\Fixtures\TestFixtures;
use Grifart\Tables\TooManyRowsFound;
use Tester\Assert;
use function Grifart\Tables\Conditions\lesserThan;

require __DIR__ . '/bootstrap.php';

$connection = connect();

$connection->nativeQuery("TRUNCATE TABLE public.bulk");
$connection->nativeQuery("INSERT INTO public.bulk (id, value) VALUES ('2e166649-da0f-4c0e-bc3a-4759aac50092', 42), ('6c554e1c-6be0-4d52-87e2-602782bba59e', -5), ('a7723ed8-ec2e-4e06-9a84-7da20532103e', 0);");

$table = new BulkTable(
	TestFixtures::createTableManager($connection),
	TestFixtures::createTypeResolver($connection),
);

[$a, $b, $c] = $table->getAll([$table->value->ascending()]);
Assert::same(-5, $a->value);
Assert::same(0, $b->value);
Assert::same(42, $c->value);

$table->updateBy(
	$table->value->is(lesserThan(0)),
	flagged: true,
);

[$a, $b, $c] = $table->getAll([$table->value->ascending()]);
Assert::true($a->flagged);
Assert::false($b->flagged);
Assert::false($c->flagged);

$table->deleteBy(
	$table->flagged->is(true),
);

$all = $table->getAll([$table->value->ascending()]);
Assert::count(2, $all);
Assert::same(0, $all[0]->value);
Assert::same(42, $all[1]->value);
