<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\BooleanType;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$type = new BooleanType();

Assert::same(true, $type->fromDatabase('t'));
Assert::same(false, $type->fromDatabase('f'));

Assert::same('t', $type->toDatabase(true));
Assert::same('f', $type->toDatabase(false));
