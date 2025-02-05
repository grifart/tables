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

$row = $table->getOneBy($table->key()->is('key1'));
Assert::same('same value', $row->getValue());

Assert::throws(fn() => $table->getOneBy($table->key()->is('key4')), RowNotFound::class);
Assert::throws(fn() => $table->getOneBy($table->value()->is('same value')), TooManyRowsFound::class);

$row = $table->findOneBy($table->key()->is('key1'));
Assert::same('same value', $row->getValue());

Assert::null($table->findOneBy($table->key()->is('key4')));
Assert::throws(fn() => $table->findOneBy($table->value()->is('same value')), TooManyRowsFound::class);

$row = $table->getFirstBy($table->key()->is('key1'));
Assert::same('same value', $row->getValue());

Assert::throws(fn() => $table->getFirstBy($table->key()->is('key4')), RowNotFound::class);

$row = $table->getFirstBy($table->value()->is('same value'), [$table->key()->ascending()]);
Assert::same('key1', $row->getKey());

$row = $table->getFirstBy($table->value()->is('same value'), [$table->key()->descending()]);
Assert::same('key2', $row->getKey());

$row = $table->findFirstBy($table->key()->is('key1'));
Assert::same('same value', $row->getValue());

Assert::null($table->findFirstBy($table->key()->is('key4')));

$row = $table->findFirstBy($table->value()->is('same value'), [$table->key()->ascending()]);
Assert::same('key1', $row->getKey());

$row = $table->findFirstBy($table->value()->is('same value'), [$table->key()->descending()]);
Assert::same('key2', $row->getKey());
