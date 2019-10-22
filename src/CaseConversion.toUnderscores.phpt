<?php declare(strict_types=1);

require __DIR__ . '/../../bootstrap.php';

use Grifart\Tables\CaseConvertion;

\Tester\Environment::setup();

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
	\Tester\Assert::same(
		$underscores,
		CaseConvertion::toUnderscores($camelCase)
	);
}
