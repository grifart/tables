<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Scaffolding;

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\DefinitionFile;
use Grifart\ClassScaffolder\DefinitionResult;
use Grifart\ClassScaffolder\FileProcessor;
use Tester\Assert;
use function Grifart\Tables\Tests\connect;


require __DIR__ . '/../bootstrap.php';

connect();

$scaffolder = new ClassGenerator();
$fileProcessor = new FileProcessor();

$results = $fileProcessor->processFile(
	DefinitionFile::from(__DIR__ . '/../Fixtures/.definition.php'),
	static function (ClassDefinition $definition) use ($scaffolder) {
		$generated = $scaffolder->generateClass($definition);

		Assert::matchFile(
			__DIR__ . '/../Fixtures/' . $definition->getClassName() . '.php',
			(string) $generated,
		);

		return DefinitionResult::success($definition);
	},
);

Assert::count(12, $results->getDefinitions());
Assert::true($results->isSuccessful());
