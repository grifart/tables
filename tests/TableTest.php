<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Grifart\Tables\Expression;
use Grifart\Tables\OrderBy\Nulls;
use Grifart\Tables\RowNotFound;
use Grifart\Tables\RowWithGivenPrimaryKeyAlreadyExists;
use Grifart\Tables\Tests\Fixtures\TestFixtures;
use Grifart\Tables\Tests\Fixtures\TestPrimaryKey;
use Grifart\Tables\Tests\Fixtures\TestsTable;
use Grifart\Tables\Tests\Fixtures\Uuid;
use Grifart\Tables\Types\BooleanType;
use Grifart\Tables\Types\IntType;
use Nette\Utils\Paginator;
use Tester\Assert;
use const Grifart\Tables\DefaultValue;
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
	TestFixtures::createTypeResolver($connection),
);

$all = $table->getAll();
Assert::count(2, $all);

$all2 = $table->findBy([]);
Assert::equal($all, $all2);

$table->insert(
	new Uuid('9493decd-4b9c-45d6-9960-0c94dc9be353'),
	-5,
	details: '👎',
);

$all = $table->getAll();
Assert::count(3, $all);

$count = $table->count();
Assert::same(3, $count);

$byId = $table->get(TestPrimaryKey::from(new Uuid('9493decd-4b9c-45d6-9960-0c94dc9be353')));
Assert::same(-5, $byId->score);
Assert::same('👎', $byId->details);

$nonNegative = $table->findBy(
	$table->score->is(greaterThanOrEqualTo(0)),
	orderBy: [$table->score],
);
Assert::count(2, $nonNegative);
Assert::same(0, $nonNegative[0]->score);
Assert::same(42, $nonNegative[1]->score);

$nonNegativeReversed = $table->findBy(
	[$table->score->is(greaterThanOrEqualTo(0))],
	orderBy: [$table->score->descending()],
);
Assert::count(2, $nonNegativeReversed);
Assert::same(42, $nonNegativeReversed[0]->score);
Assert::same(0, $nonNegativeReversed[1]->score);

$countBy = $table->count($table->score->is(greaterThanOrEqualTo(0)));
Assert::same(2, $countBy);

$orderByNullsFirst = $table->findBy([], orderBy: [$table->details->ascending(Nulls::First)]);
Assert::count(3, $orderByNullsFirst);
Assert::same(null, $orderByNullsFirst[0]->details);

$orderByNullsLast = $table->findBy([], orderBy: [$table->details->ascending(Nulls::Last)]);
Assert::count(3, $orderByNullsLast);
Assert::same(null, $orderByNullsLast[2]->details);

$orderByValues = $table->findBy([], orderBy: [$table->id->byValues([
	new Uuid('2bec3f23-a210-455c-b907-bb69a99d07b2'),
	new Uuid('9493decd-4b9c-45d6-9960-0c94dc9be353'),
	new Uuid('fb05a832-5729-4b1f-b064-fbc08cacbe43'),
])]);
Assert::count(3, $orderByValues);
Assert::same('2bec3f23-a210-455c-b907-bb69a99d07b2', $orderByValues[0]->id->get());
Assert::same('9493decd-4b9c-45d6-9960-0c94dc9be353', $orderByValues[1]->id->get());
Assert::same('fb05a832-5729-4b1f-b064-fbc08cacbe43', $orderByValues[2]->id->get());

$paginator = new Paginator();
$paginator->setItemsPerPage(1);
$paginator->setPage(2);
$nonNegativePaginated = $table->findBy(
	$table->score->is(greaterThanOrEqualTo(0)),
	orderBy: [$table->score],
	paginator: $paginator,
);
Assert::count(1, $nonNegativePaginated);
Assert::same(42, $nonNegativePaginated[0]->score);
Assert::same(2, $paginator->getItemCount());

$abs = static fn(Expression $sub) => expr(IntType::integer(), 'ABS(?)', $sub);
$filteredByExpression = $table->findBy(
	[$abs($table->score)->is(lesserThanOrEqualTo(5))],
	orderBy: [$abs($table->score)],
);
Assert::count(2, $filteredByExpression);
Assert::same(0, $filteredByExpression[0]->score);
Assert::same(-5, $filteredByExpression[1]->score);

$startsWith = static fn(Expression $sub, string $prefix) => expr(new BooleanType(), 'starts_with(?, %s)', $sub, $prefix);
$filteredByAnotherExpression = $table->findBy($startsWith($table->details, '👎')->is(equalTo(true)));
Assert::count(1, $filteredByAnotherExpression);
Assert::same(-5, $filteredByAnotherExpression[0]->score);

$nullDetails = $table->findBy($table->details->is(null));
Assert::count(1, $nullDetails);
Assert::same(0, $nullDetails[0]->score);

$unique = $table->getUniqueBy($table->score->is(42));
Assert::same(42, $unique->score);

$table->update(
	TestPrimaryKey::from(new Uuid('2bec3f23-a210-455c-b907-bb69a99d07b2')),
	details: 'nada',
);

$updatedZero = $table->get(TestPrimaryKey::from(new Uuid('2bec3f23-a210-455c-b907-bb69a99d07b2')));

Assert::same(0, $updatedZero->score);
Assert::same('nada', $updatedZero->details);

$table->delete(TestPrimaryKey::fromRow($updatedZero));
Assert::null($table->find(TestPrimaryKey::fromRow($updatedZero)));

$newRow = $table->insertAndGet($id = new Uuid('7ec810dd-4d52-4bb9-ae96-6f558ee4890f'), 7);
Assert::same(7, $newRow->score);

$updatedRow = $table->updateAndGet($newRow, score: -7);
Assert::same(-7, $updatedRow->score);

// upsert

Assert::throws(fn() => $table->insert($id, 11), RowWithGivenPrimaryKeyAlreadyExists::class);

$table->upsert($id, 17);
Assert::same(17, $table->get(TestPrimaryKey::from($id))->score);

$upsertedRow = $table->upsertAndGet($id, 11);
Assert::same(11, $upsertedRow->score);

// deleteAndGet

$deleted = $table->deleteAndGet(TestPrimaryKey::fromRow($upsertedRow));
Assert::same(11, $deleted->score);
Assert::throws(fn() => $table->get(TestPrimaryKey::fromRow($deleted)), RowNotFound::class);

// save

$new = $table->new($newId = new Uuid('003a486e-6111-4f92-bfee-047e798896a1'), 999_999);
$table->save($new);

$row = $table->get(TestPrimaryKey::from($newId));
Assert::same(999_999, $row->score);
Assert::null($row->details);

$edit = $table->edit($row, score: -999_999, details: 'test');
$table->save($edit);

$row = $table->get(TestPrimaryKey::from($newId));
Assert::same(-999_999, $row->score);
Assert::same('test', $row->details);

// update with default

$row = $table->updateAndGet(TestPrimaryKey::from($newId), details: DefaultValue);
Assert::same(null, $row->details);
