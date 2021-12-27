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

$nestedArrayType = ArrayType::of(ArrayType::of(new IntType()));
Assert::same('{{4,8},{15,16},{23,42}}', $nestedArrayType->toDatabase([[4, 8], [15, 16], [23, 42]]));
Assert::same([[4, 8], [15, 16], [23, 42]], $nestedArrayType->fromDatabase('{{4,8},{15,16},{23,42}}'));

$textArrayType = ArrayType::of(new TextType());
Assert::same('{simple,NULL,"","co,m\\\\ple\\"x"}', $textArrayType->toDatabase(['simple', null, '', 'co,m\\ple"x']));
Assert::same(['simple', null, '', 'co,m\\ple"x'], $textArrayType->fromDatabase('{simple,NULL,"","co,m\\\\ple\\"x"}'));
