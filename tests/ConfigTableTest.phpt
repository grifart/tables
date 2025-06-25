<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Grifart\Tables\RowNotFound;
use Grifart\Tables\Tests\Fixtures\ConfigTable;
use Grifart\Tables\Tests\Fixtures\TestFixtures;
use Grifart\Tables\TooManyRowsFound;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$connection = connect();

$connection->nativeQuery("TRUNCATE TABLE public.config");
$connection->nativeQuery("INSERT INTO public.config (id, key, value) VALUES ('4bd6f9a9-cccf-4e1d-bbdd-bcd89406f65a', 'key1', 'same value'), ('d9d29fb0-6c6e-48b9-a60f-8c8136fe0840', 'key2', 'same value'), ('c10c5e42-a2fe-4fbd-97f7-0d4d3e27541e', 'key3', 'different value');");

$table = new ConfigTable(
	TestFixtures::createTableManager($connection),
	TestFixtures::createTypeResolver($connection),
);

$row = $table->getUniqueBy($table->key->is('key1'));
Assert::same('same value', $row->value);

Assert::throws(fn() => $table->getUniqueBy($table->key->is('key4')), RowNotFound::class);
Assert::throws(fn() => $table->getUniqueBy($table->value->is('same value')), TooManyRowsFound::class);

$row = $table->findUniqueBy($table->key->is('key1'));
Assert::same('same value', $row->value);

Assert::null($table->findUniqueBy($table->key->is('key4')));
Assert::throws(fn() => $table->findUniqueBy($table->value->is('same value')), TooManyRowsFound::class);

$row = $table->getFirstBy($table->key->is('key1'));
Assert::same('same value', $row->value);

Assert::throws(fn() => $table->getFirstBy($table->key->is('key4')), RowNotFound::class);

$row = $table->getFirstBy($table->value->is('same value'), [$table->key->ascending()]);
Assert::same('key1', $row->key);

$row = $table->getFirstBy($table->value->is('same value'), [$table->key->descending()]);
Assert::same('key2', $row->key);

$row = $table->findFirstBy($table->key->is('key1'));
Assert::same('same value', $row->value);

Assert::null($table->findFirstBy($table->key->is('key4')));

$row = $table->findFirstBy($table->value->is('same value'), [$table->key->ascending()]);
Assert::same('key1', $row->key);

$row = $table->findFirstBy($table->value->is('same value'), [$table->key->descending()]);
Assert::same('key2', $row->key);
