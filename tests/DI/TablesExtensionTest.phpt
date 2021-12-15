<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\DI;

use Dibi\Connection;
use Grifart\Tables\TableManager;
use Grifart\Tables\Tests\Fixtures\UuidType;
use Grifart\Tables\TypeResolver;
use Grifart\Tables\Types\IntType;
use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use Tester\Assert;
use Tester\Helpers;

require __DIR__ . '/../bootstrap.php';

$connection = require __DIR__ . '/../createConnection.local.php';
\assert($connection instanceof Connection);

\define('TEMP_DIR', __DIR__ . '/temp/' . (isset($_SERVER['argv']) ? \md5(\serialize($_SERVER['argv'])) : \getmypid()));
Helpers::purge(\TEMP_DIR);

$createContainer = function (string $configFile) use ($connection): Container
{
	$configurator = new Configurator();
	$configurator->setTempDirectory(TEMP_DIR);
	$configurator->setDebugMode(false);

	$configurator->addConfig(__DIR__ . '/config/common.neon');
	$configurator->addConfig(__DIR__ . '/config/' . $configFile . '.neon');

	$configurator->addServices(['connection' => $connection]);

	return $configurator->createContainer();
};

(function () use ($createContainer) {
	$container = $createContainer('default');
	Assert::type(TableManager::class, $container->getByType(TableManager::class));
})();

(function () use ($createContainer) {
	$container = $createContainer('types');

	$typeResolver = $container->getByType(TypeResolver::class);
	Assert::type(TypeResolver::class, $typeResolver);

	Assert::type(UuidType::class, $typeResolver->resolveType('uuid', 'public.test.id'));
	Assert::type(IntType::class, $typeResolver->resolveType('int', 'public.test.score'));
})();
