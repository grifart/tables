<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Brick\DateTime\LocalDate;
use Grifart\Tables\Types\DateType;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$type = new DateType();

Assert::same('2021-12-15', (string) $type->fromDatabase('2021-12-15'));
Assert::same("'2021-12-15'", $connection->translate($type->toDatabase(LocalDate::of(2021, 12, 15))));
