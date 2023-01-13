<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\BinaryType;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$type = new BinaryType();

Assert::same("\xde\xad\xbe\xef", $type->fromDatabase('\xdeadbeef'));
Assert::same("'\\xdeadbeef'", $connection->translate($type->toDatabase("\xde\xad\xbe\xef")));
