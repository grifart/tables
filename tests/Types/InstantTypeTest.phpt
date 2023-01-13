<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Brick\DateTime\Instant;
use Grifart\Tables\Types\InstantType;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$type = new InstantType();

$timestamp = 1639573200;

Assert::same($timestamp, $type->fromDatabase('2021-12-15 13:00:00')->getEpochSecond());
Assert::same("'2021-12-15T13:00Z'", $connection->translate($type->toDatabase(Instant::of($timestamp))));
