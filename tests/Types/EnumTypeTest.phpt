<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Database\NamedType;
use Grifart\Tables\Types\EnumType;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

/**
 * @phpVersion 8.1
 */

enum Status: string
{
	case DRAFT = 'draft';
	case PUBLISHED = 'published';
}

$enumType = EnumType::of(Status::class, new NamedType(new Identifier('text')));

Assert::same("'draft'", $connection->translate($enumType->toDatabase(Status::DRAFT)));
Assert::same(Status::PUBLISHED, $enumType->fromDatabase('published'));
