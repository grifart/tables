<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Dibi\Expression;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Database\NamedType;
use Grifart\Tables\Types\CompositeType;
use Grifart\Tables\Types\IntType;
use Grifart\Tables\Types\NullableType;
use Grifart\Tables\Types\TextType;
use Grifart\Tables\UnexpectedNullValue;
use Tester\Assert;
use function Grifart\ClassScaffolder\Definition\Types\nullable;
use function Grifart\ClassScaffolder\Definition\Types\tuple;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

$connection = connect();

$composite = new class extends CompositeType {
	public function __construct()
	{
		parent::__construct(
			new NamedType(new Identifier('databaseType')),
			IntType::integer(),
			NullableType::of(IntType::integer()),
			TextType::text(),
			TextType::text(),
			TextType::text(),
			NullableType::of(TextType::text()),
		);
	}

	public function getPhpType(): PhpType
	{
		return tuple('int', nullable('int'), 'string', 'string', nullable('string'));
	}

	public function toDatabase(mixed $value): Expression
	{
		return $this->tupleToDatabase($value);
	}

	public function fromDatabase(mixed $value): mixed
	{
		return $this->tupleFromDatabase($value);
	}
};

Assert::same(
	"ROW(42,NULL,'com\\ple\"''x','(','',NULL)::\"databaseType\"",
	$connection->translate($composite->toDatabase([42, null, 'com\\ple"\'x', '(', '', null])),
);

Assert::same([42, null, 'com\\ple"\'x', '(', '', null], $composite->fromDatabase('(42,,"com\\\\ple""\'x","(","",)'));

Assert::throws(fn() => $composite->toDatabase([null, null, 'foo', '', '', null]), UnexpectedNullValue::class);
Assert::throws(fn() => $composite->fromDatabase('(,,"foo","","",)'), UnexpectedNullValue::class);
