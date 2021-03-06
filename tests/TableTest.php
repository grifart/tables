<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Grifart\Tables\Expression;
use Grifart\Tables\Tests\Fixtures\TestFixtures;
use Grifart\Tables\Tests\Fixtures\TestPrimaryKey;
use Grifart\Tables\Tests\Fixtures\TestsTable;
use Grifart\Tables\Tests\Fixtures\Uuid;
use Grifart\Tables\Types\BooleanType;
use Grifart\Tables\Types\IntType;
use Nette\Utils\Paginator;
use Tester\Assert;
use function Grifart\Tables\Conditions\equalTo;
use function Grifart\Tables\Conditions\greaterThanOrEqualTo;
use function Grifart\Tables\Conditions\lesserThanOrEqualTo;
use function Grifart\Tables\expr;

require __DIR__ . '/bootstrap.php';

$connection = connect();

$connection->nativeQuery("TRUNCATE TABLE public.test");
$connection->nativeQuery("INSERT INTO public.test (id, score, details) VALUES ('fb05a832-5729-4b1f-b064-fbc08cacbe43', 42, '👍'), ('2bec3f23-a210-455c-b907-bb69a99d07b2', 0, NULL);");

$table = new TestsTable(
	TestFixtures::createTableManager($connection),
	TestFixtures::createTypeResolver(),
);

$all = $table->getAll();
Assert::count(2, $all);

$all2 = $table->findBy([]);
Assert::equal($all, $all2);

$changeSet = $table->new(new Uuid('9493decd-4b9c-45d6-9960-0c94dc9be353'), -5);
$changeSet->modifyDetails('👎');
$table->save($changeSet);

$all = $table->getAll();
Assert::count(3, $all);

$byId = $table->get(TestPrimaryKey::from(new Uuid('9493decd-4b9c-45d6-9960-0c94dc9be353')));
Assert::same(-5, $byId->getScore());
Assert::same('👎', $byId->getDetails());

$nonNegative = $table->findBy(
	$table->score()->is(greaterThanOrEqualTo(0)),
	orderBy: [$table->score()],
);
Assert::count(2, $nonNegative);
Assert::same(0, $nonNegative[0]->getScore());
Assert::same(42, $nonNegative[1]->getScore());

$nonNegativeReversed = $table->findBy(
	[$table->score()->is(greaterThanOrEqualTo(0))],
	orderBy: [$table->score()->descending()],
);
Assert::count(2, $nonNegativeReversed);
Assert::same(42, $nonNegativeReversed[0]->getScore());
Assert::same(0, $nonNegativeReversed[1]->getScore());

$paginator = new Paginator();
$paginator->setItemsPerPage(1);
$paginator->setPage(2);
$nonNegativePaginated = $table->findBy(
	$table->score()->is(greaterThanOrEqualTo(0)),
	orderBy: [$table->score()],
	paginator: $paginator,
);
Assert::count(1, $nonNegativePaginated);
Assert::same(42, $nonNegativePaginated[0]->getScore());
Assert::same(2, $paginator->getItemCount());

$abs = static fn(Expression $sub) => expr(new IntType(), 'ABS(?)', $sub);
$filteredByExpression = $table->findBy(
	[$abs($table->score())->is(lesserThanOrEqualTo(5))],
	orderBy: [$abs($table->score())],
);
Assert::count(2, $filteredByExpression);
Assert::same(0, $filteredByExpression[0]->getScore());
Assert::same(-5, $filteredByExpression[1]->getScore());

$startsWith = static fn(Expression $sub, string $prefix) => expr(new BooleanType(), 'starts_with(?, %s)', $sub, $prefix);
$filteredByAnotherExpression = $table->findBy($startsWith($table->details(), '👎')->is(equalTo(true)));
Assert::count(1, $filteredByAnotherExpression);
Assert::same(-5, $filteredByAnotherExpression[0]->getScore());

$nullDetails = $table->findBy($table->details()->is(null));
Assert::count(1, $nullDetails);
Assert::same(0, $nullDetails[0]->getScore());

$unique = $table->getBy($table->score()->is(42));
Assert::same(42, $unique->getScore());

$zero = $table->get(TestPrimaryKey::from(new Uuid('2bec3f23-a210-455c-b907-bb69a99d07b2')));
$zeroChangeSet = $table->edit($zero);
$zeroChangeSet->modifyDetails('nada');
$table->save($zeroChangeSet);

$updatedZero = $table->get(TestPrimaryKey::from(new Uuid('2bec3f23-a210-455c-b907-bb69a99d07b2')));
Assert::same('nada', $updatedZero->getDetails());

$table->delete(TestPrimaryKey::fromRow($updatedZero));
Assert::null($table->find(TestPrimaryKey::fromRow($updatedZero)));
