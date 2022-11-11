<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\TextType;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$type = TextType::text();

Assert::same('string', $type->fromDatabase('string'));

Assert::same("'string'", $connection->translate($type->toDatabase('string')));
