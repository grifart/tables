<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\Tables\Types\ArrayType;
use Grifart\Tables\Types\IntType;
use Grifart\Tables\Types\NullableType;
use Grifart\Tables\Types\TextType;
use Grifart\Tables\UnexpectedNullValue;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;
use function Grifart\Tables\Tests\executeExpressionInDatabase;


require __DIR__ . '/../bootstrap.php';

$connection = connect();

(function() use ($connection) {
	$theInput = [42, null, -5];
	$intArrayType = ArrayType::of(NullableType::of(IntType::integer()));
	Assert::same('ARRAY[42,NULL,-5]::integer[]', $connection->translate($dbExpr = $intArrayType->toDatabase($theInput)));
	$dbResult = executeExpressionInDatabase($connection, $dbExpr);
	Assert::same('{42,NULL,-5}', $dbResult);
	Assert::same($theInput, $intArrayType->fromDatabase($dbResult));
})();

(function() use ($connection) {
	$theInput = [[4, 8], [15, 16], [23, 42]];
	$nestedArrayType = ArrayType::of(ArrayType::of(IntType::integer()));
	Assert::same('ARRAY[ARRAY[4,8]::integer[],ARRAY[15,16]::integer[],ARRAY[23,42]::integer[]]::integer[][]', $connection->translate($dbExpr = $nestedArrayType->toDatabase($theInput)));
	$dbResult = executeExpressionInDatabase($connection, $dbExpr);
	Assert::same('{{4,8},{15,16},{23,42}}', $dbResult);
	Assert::same($theInput, $nestedArrayType->fromDatabase($dbResult));
})();

(function() use ($connection) {
	$theInput = ['simple', '', 'co,m\\ple"\'x'];
	$textArrayType = ArrayType::of(TextType::text());
	Assert::same("ARRAY['simple','','co,m\\ple\"''x']::text[]", $connection->translate($dbExpr = $textArrayType->toDatabase($theInput)));
	$dbResult = executeExpressionInDatabase($connection, $dbExpr);
	Assert::same('{simple,"","co,m\\\\ple\\"\'x"}', $dbResult);
	Assert::same($theInput, $textArrayType->fromDatabase($dbResult));
})();

(function() {
	$textArrayType = ArrayType::of(TextType::text());
	Assert::throws(fn() => $textArrayType->toDatabase([null]), UnexpectedNullValue::class);
	Assert::throws(fn() => $textArrayType->fromDatabase('{NULL}'), UnexpectedNullValue::class);
})();
