<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$type = new UuidType();

Assert::type(UuidInterface::class, $type->fromDatabase('ee2e75cd-2767-44d4-bf25-b4e19954f70f'));
Assert::same('ee2e75cd-2767-44d4-bf25-b4e19954f70f', $type->fromDatabase('ee2e75cd-2767-44d4-bf25-b4e19954f70f')->toString());

Assert::same("'ee2e75cd-2767-44d4-bf25-b4e19954f70f'", $connection->translate($type->toDatabase(Uuid::fromString('ee2e75cd-2767-44d4-bf25-b4e19954f70f'))));
