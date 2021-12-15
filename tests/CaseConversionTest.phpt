<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests;

use Grifart\Tables\CaseConversion;
use Tester\Assert;
use Tester\Environment;

require __DIR__ . '/bootstrap.php';

Environment::setup();

$testCases = [
	'lowercase' => 'LOWERCASE',
	'Class' => 'CLASS',
	'MyClass' => 'MY_CLASS',
	'HTML' => 'HTML',
	'PDFLoader' => 'PDF_LOADER',
	'AString' => 'A_STRING',
	'SimpleXMLParser' => 'SIMPLE_XML_PARSER',
	'GL11Version' => 'GL_11_VERSION',
	'99Bottles' => '99_BOTTLES',
	'May5' => 'MAY_5',
	'BFG9000' => 'BFG_9000',
];


foreach($testCases as $camelCase => $underscores) {
	Assert::same(
		$underscores,
		CaseConversion::toUnderscores($camelCase)
	);
}
