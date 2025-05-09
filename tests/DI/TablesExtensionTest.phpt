<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\DI;

use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Scaffolding\TablesDefinitions;
use Grifart\Tables\SingleConnectionTableManager;
use Grifart\Tables\TableManager;
use Grifart\Tables\Tests\Fixtures\VersionType;
use Grifart\Tables\TypeResolver;
use Grifart\Tables\Types\DecimalType;
use Grifart\Tables\Types\IntType;
use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use Tester\Assert;
use Tester\Helpers;
use function Grifart\Tables\Tests\connect;

require __DIR__ . '/../bootstrap.php';

\define('TEMP_DIR', __DIR__ . '/temp/' . (isset($_SERVER['argv']) ? \md5(\serialize($_SERVER['argv'])) : \getmypid()));
Helpers::purge(\TEMP_DIR);

$createContainer = function (string $configFile): Container
{
	$configurator = new Configurator();
	$configurator->setTempDirectory(TEMP_DIR);
	$configurator->setDebugMode(false);

	$configurator->addConfig(__DIR__ . '/config/common.neon');
	$configurator->addConfig(__DIR__ . '/config/' . $configFile . '.neon');

	$configurator->addServices(['connection' => connect()]);

	return $configurator->createContainer();
};

(function () use ($createContainer) {
	$container = $createContainer('default');
	Assert::type(SingleConnectionTableManager::class, $container->getByType(TableManager::class));
	Assert::type(TablesDefinitions::class, $container->getByType(TablesDefinitions::class));
})();

(function () use ($createContainer) {
	$container = $createContainer('types');

	$typeResolver = $container->getByType(TypeResolver::class);
	Assert::type(TypeResolver::class, $typeResolver);
	Assert::type(IntType::class, $typeResolver->resolveType('int', new Identifier('public', 'test', 'score')));
	Assert::type(VersionType::class, $typeResolver->resolveType('public."packageVersion"', new Identifier('public', 'test', 'whatever')));
})();

(function () use ($createContainer) {
	$container = $createContainer('typeResolver');

	$typeResolver = $container->getByType(TypeResolver::class);
	Assert::type(TypeResolver::class, $typeResolver);
	Assert::type(DecimalType::class, $typeResolver->resolveType('boolean', new Identifier('public', 'test', 'whatever')));
})();

(function () use ($createContainer) {
	$container = $createContainer('typeConfigurator');

	$typeResolver = $container->getByType(TypeResolver::class);
	Assert::type(TypeResolver::class, $typeResolver);
	Assert::type(DecimalType::class, $typeResolver->resolveType('boolean', new Identifier('public', 'test', 'whatever2')));
})();
