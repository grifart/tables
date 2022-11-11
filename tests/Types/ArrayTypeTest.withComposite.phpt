<?php declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Database\NamedType;
use Grifart\Tables\Types\ArrayType;
use Grifart\Tables\Types\CompositeType;
use Grifart\Tables\Types\IntType;
use Tester\Assert;
use function Grifart\ClassScaffolder\Definition\Types\resolve;
use function Grifart\Tables\Tests\connect;
use function Grifart\Tables\Tests\executeExpressionInDatabase;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

final class Version {
	public function __construct(public int $major, public int $minor, public int $patch) {}
	/** @return array{int,int,int} */
	public function toArray(): array { return [$this->major, $this->minor, $this->patch]; }
}

/** @extends CompositeType<Version> */
final class VersionType extends CompositeType {
	public function __construct() { parent::__construct(new NamedType(new Identifier('public', 'packageVersion')), IntType::integer(), IntType::integer(), IntType::integer() ); }
	public function getPhpType(): PhpType { return resolve(Version::class); }
	public function toDatabase(mixed $value): Expression { return $this->tupleToDatabase($value->toArray()); }
	public function fromDatabase(mixed $value): Version { return new Version(...$this->tupleFromDatabase($value)); }
}

$theInput = [new Version(0,1,0), new Version(1,0,0), new Version(1,0,1)];
$compositeArrayType = ArrayType::of(new VersionType());
Assert::same('ARRAY[ROW(0,1,0)::public."packageVersion",ROW(1,0,0)::public."packageVersion",ROW(1,0,1)::public."packageVersion"]::public."packageVersion"[]', $connection->translate($dbExpr = $compositeArrayType->toDatabase($theInput)));
$dbResult = executeExpressionInDatabase($connection, $dbExpr);
Assert::same('{"(0,1,0)","(1,0,0)","(1,0,1)"}', $dbResult);
Assert::equal($theInput, $compositeArrayType->fromDatabase($dbResult));
