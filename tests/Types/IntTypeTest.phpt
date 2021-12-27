<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\IntType;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$type = new IntType();

Assert::same(42, $type->fromDatabase(42));
Assert::same(42, $type->fromDatabase('42'));

Assert::same(42, $type->toDatabase(42));
Assert::same(42, $type->toDatabase('42'));
