<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Brick\DateTime\LocalTime;
use Grifart\Tables\Types\TimeType;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$type = new TimeType();

Assert::same(null, $type->fromDatabase(null));
Assert::same('13:00', (string) $type->fromDatabase('13:00:00'));

Assert::same(null, $type->toDatabase(null));
Assert::same('13:00', $type->toDatabase(LocalTime::of(13, 0)));
