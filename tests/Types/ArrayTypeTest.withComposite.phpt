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
$theInput = [new Version(0,1,0), new Version(1,0,0), new Version(1,0,1)];
$compositeArrayType = ArrayType::of(new NamedIdentifier('public', 'packageVersion'), new VersionType());
Assert::same('ARRAY[ROW(0,1,0)::public."packageVersion",ROW(1,0,0)::public."packageVersion",ROW(1,0,1)::public."packageVersion"]::public."packageVersion"[]', (string) $compositeArrayType->toDatabase($theInput));

/** @var callable(Version[] $versions):array<array{int,int,int}> $toTuples */
// scalar tuples are comparable with ===
$toTuples = static fn(array $versions): array => map($versions, static fn($version) => $version->toArray());
Assert::same(
	$toTuples($theInput),
	$toTuples($compositeArrayType->fromDatabase('{(0,1,0),(1,0,0),(1,0,1)}')),
);
