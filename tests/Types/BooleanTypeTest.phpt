<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\BooleanType;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$type = new BooleanType();

Assert::same(true, $type->fromDatabase('t'));
Assert::same(false, $type->fromDatabase('f'));

Assert::same('TRUE', $connection->translate($type->toDatabase(true)));
Assert::same('FALSE', $connection->translate($type->toDatabase(false)));
