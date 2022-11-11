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


final class Version {
	public function __construct(public int $major, public int $minor, public int $patch) {}
	/** @return array{int,int,int} */
	public function toArray(): array { return [$this->major, $this->minor, $this->patch]; }
}
/** @extends CompositeType<Version> */
final class VersionType extends CompositeType {
	public function __construct() { parent::__construct(new NamedIdentifier('public', 'packageVersion'), new IntType(), new IntType(), new IntType() ); }
	public function getPhpType(): PhpType { return resolve(Version::class); }
	public function toDatabase(mixed $value): Literal { return $this->tupleToDatabase($value->toArray()); }
	public function fromDatabase(mixed $value): Version { return new Version(...$this->tupleFromDatabase($value)); }
};
$compositeArrayType = ArrayType::of(new VersionType());
Assert::same('{(0,1,0),(1,0,0),(1,0,1)}::public."packageVersion"[]', $compositeArrayType->toDatabase([new Version(0,1,0), new Version(1,0,0), new Version(1,0,1)]));

/** @var callable(Version[] $versions):array<array{int,int,int}> $toTuples */
// scalar tuples are comparable with ===
$toTuples = static fn(array $versions): array => map($versions, static fn($version) => $version->toArray());
Assert::same(
	$toTuples([new Version(0,1,0), new Version(1,0,0), new Version(1,0,1)]),
	$toTuples($compositeArrayType->fromDatabase('{(0,1,0),(1,0,0),(1,0,1)}')),
);
