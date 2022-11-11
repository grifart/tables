<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\IntType;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$type = IntType::integer();

Assert::same(42, $type->fromDatabase(42));
Assert::same(42, $type->fromDatabase('42'));

Assert::same('42', $connection->translate($type->toDatabase(42)));
Assert::same('42', $connection->translate($type->toDatabase('42')));
