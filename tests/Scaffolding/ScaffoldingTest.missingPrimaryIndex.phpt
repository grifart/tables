<?php declare(strict_types = 1);

namespace Grifart\Tables\Tests\Scaffolding;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\DefinitionFile;
use Grifart\ClassScaffolder\DefinitionResult;
use Grifart\ClassScaffolder\FileProcessor;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;


require __DIR__ . '/../bootstrap.php';

connect();

$fileProcessor = new FileProcessor();

$results = $fileProcessor->processFile(
	DefinitionFile::from(__DIR__ . '/../Fixtures/.definition.missingPrimaryIndex.php'),
	static fn(ClassDefinition $definition) => DefinitionResult::success($definition), // won't be called but has to return something
);

Assert::true( ! $results->isSuccessful());
Assert::same('Table "public"."missingPrimaryIndex" must have a primary index. Provide one and try again.', $results->getError()->getMessage());
