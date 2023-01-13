<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Brick\Math\BigDecimal;
use Grifart\Tables\Types\DecimalType;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$type = DecimalType::decimal();

Assert::same('42', (string) $type->fromDatabase(42.0));
Assert::same('42.0', (string) $type->fromDatabase('42.0'));
Assert::same("'42'", $connection->translate($type->toDatabase(BigDecimal::of(42.0))));
