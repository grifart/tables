<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\ArrayType;
use Grifart\Tables\Types\IntType;
use Grifart\Tables\Types\TextType;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$intArrayType = ArrayType::of(new IntType());
Assert::same('{42,NULL,-5}', $intArrayType->toDatabase([42, null, -5]));
Assert::same([42, null, -5], $intArrayType->fromDatabase('{42,NULL,-5}'));

$textArrayType = ArrayType::of(new TextType());
Assert::same('{simple,NULL,"com\\\\ple\\"x"}', $textArrayType->toDatabase(['simple', null, 'com\\ple"x']));
Assert::same(['simple', null, 'com\\ple"x'], $textArrayType->fromDatabase('{simple,NULL,"com\\\\ple\\"x"}'));
