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
