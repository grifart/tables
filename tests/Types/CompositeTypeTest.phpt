<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Types;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Types\CompositeType;
use Grifart\Tables\Types\IntType;
use Grifart\Tables\Types\TextType;
use Tester\Assert;
use function Grifart\ClassScaffolder\Definition\Types\nullable;
use function Grifart\ClassScaffolder\Definition\Types\tuple;

require __DIR__ . '/../bootstrap.php';

$composite = new class extends CompositeType {
	public function __construct()
	{
		parent::__construct(
			new IntType(),
			new IntType(),
			new TextType(),
			new TextType(),
		);
	}

	public function getPhpType(): PhpType
	{
		return tuple('int', nullable('int'), 'string');
	}

	public function toDatabase(mixed $value): mixed
	{
		return $this->tupleToDatabase($value);
	}

	public function fromDatabase(mixed $value): mixed
	{
		return $this->tupleFromDatabase($value);
	}
};

Assert::same('(42,,"com\\\\ple\\"x","(")', $composite->toDatabase([42, null, 'com\\ple"x', '(']));
Assert::same([42, null, 'com\\ple"x', '('], $composite->fromDatabase('(42,,"com\\\\ple""x","(")'));
