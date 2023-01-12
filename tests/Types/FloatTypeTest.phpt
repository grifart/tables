<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\FloatType;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;
use function is_nan;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$type = FloatType::double();

Assert::same(42.0, $type->fromDatabase(42.0));
Assert::same(0.5, $type->fromDatabase(0.5));
Assert::same(INF, $type->fromDatabase('Infinity'));
Assert::same(-INF, $type->fromDatabase('-Infinity'));
Assert::true(is_nan($type->fromDatabase('NaN')));

Assert::same('42', $connection->translate($type->toDatabase(42.0)));
Assert::same('0.5', $connection->translate($type->toDatabase(0.5)));
Assert::same("'Infinity'", $connection->translate($type->toDatabase(INF)));
Assert::same("'-Infinity'", $connection->translate($type->toDatabase(-INF)));
Assert::same("'NaN'", $connection->translate($type->toDatabase(NAN)));
