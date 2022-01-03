<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\EnumType;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @phpVersion 8.1
 */

enum Status: string
{
	case DRAFT = 'draft';
	case PUBLISHED = 'published';
}

$enumType = EnumType::of(Status::class);

Assert::same('draft', $enumType->toDatabase(Status::DRAFT));
Assert::same(Status::PUBLISHED, $enumType->fromDatabase('published'));
