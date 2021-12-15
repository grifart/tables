<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Brick\Math\BigDecimal;
use Grifart\Tables\Types\DecimalType;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$type = new DecimalType();

Assert::same(null, $type->fromDatabase(null));
Assert::same('42', (string) $type->fromDatabase(42.0));
Assert::same('42.0', (string) $type->fromDatabase('42.0'));

Assert::same(null, $type->toDatabase(null));
Assert::same('42', $type->toDatabase(BigDecimal::of(42.0)));
