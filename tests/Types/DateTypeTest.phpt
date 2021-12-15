<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Brick\DateTime\LocalDate;
use Grifart\Tables\Types\DateType;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$type = new DateType();

Assert::same(null, $type->fromDatabase(null));
Assert::same('2021-12-15', (string) $type->fromDatabase('2021-12-15'));

Assert::same(null, $type->toDatabase(null));
Assert::same('2021-12-15', $type->toDatabase(LocalDate::of(2021, 12, 15)));
