<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\TextType;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$type = new TextType();

Assert::same(null, $type->fromDatabase(null));
Assert::same('string', $type->fromDatabase('string'));

Assert::same(null, $type->toDatabase(null));
Assert::same('string', $type->toDatabase('string'));
