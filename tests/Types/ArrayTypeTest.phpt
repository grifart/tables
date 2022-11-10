<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Dibi\Literal;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\NamedIdentifier;
use Grifart\Tables\Types\ArrayType;
use Grifart\Tables\Types\CompositeType;
use Grifart\Tables\Types\IntType;
use Grifart\Tables\Types\TextType;
use PhpParser\Node\Name;
use Tester\Assert;
use function Functional\map;
use function Grifart\ClassScaffolder\Definition\Types\resolve;
use function Grifart\Tables\Tests\connect;


require __DIR__ . '/../bootstrap.php';

$connection = connect();
$executeExpressionInDatabase = function(string $expression) use ($connection): string {
	$result = $connection->nativeQuery(sprintf("SELECT %s;", $expression))->fetchSingle();
	\assert(is_string($result));
	return $connection->nativeQuery(sprintf("SELECT %s;", $expression))->fetchSingle();
};

(function() use ($executeExpressionInDatabase) {
	$theInput = [42, null, -5];
	$intArrayType = ArrayType::of(new NamedIdentifier('int'), new IntType());
	Assert::same('ARRAY[42,null,-5]::int[]', $expr1 = (string) $intArrayType->toDatabase($theInput));
	$result1 = $executeExpressionInDatabase($expr1);
	Assert::same('{42,NULL,-5}', $result1);
	Assert::same($theInput, $intArrayType->fromDatabase($result1));
})();

(function() use ($executeExpressionInDatabase) {
	$theInput = [[4, 8], [15, 16], [23, 42]];
	$nestedArrayType = ArrayType::of('int[][]', ArrayType::of('int[]', new IntType()));
	Assert::same('ARRAY[ARRAY[4,8]::int[],ARRAY[15,16]::int[],ARRAY[23,42]::int[]]::int[][]', $dbExpr = (string) $nestedArrayType->toDatabase($theInput));
	$dbResult = $executeExpressionInDatabase($dbExpr);
	Assert::same('{{4,8},{15,16},{23,42}}', $dbResult);
	Assert::same($theInput, $nestedArrayType->fromDatabase($dbResult));
})();

(function() use ($executeExpressionInDatabase) {
	$theInput = ['simple', null, '', 'co,m\\ple"\'x'];
	$textArrayType = ArrayType::of('text[]', new TextType());
	Assert::same("ARRAY['simple',null,'','co,m\\\\ple\"''x']::text[]", $dbExpr = (string) $textArrayType->toDatabase($theInput));
	$dbResult = $executeExpressionInDatabase($dbExpr);
	Assert::same('{simple,NULL,"","co,m\\\\\\\\ple\\"\'x"}', $dbResult);
	Assert::same($theInput, $textArrayType->fromDatabase($dbResult));
})();

