<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\BinaryType;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$type = new BinaryType();

Assert::same("\xde\xad\xbe\xef", $type->fromDatabase('\xdeadbeef'));
Assert::same('\xdeadbeef', $type->toDatabase("\xde\xad\xbe\xef"));
