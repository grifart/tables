<?php

declare(strict_types=1);

namespace Grifart\Tables\DI;

use Grifart\Tables\Database\Identifier;
use Grifart\Tables\Scaffolding\PostgresReflector;
use Grifart\Tables\Scaffolding\TablesDefinitions;
use Grifart\Tables\TableManager;
use Grifart\Tables\TypeResolver;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use function explode;
use function is_int;

/**
 * @property-read \stdClass $config
 */
final class TablesExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'typeResolver' => Expect::anyOf(Expect::type(Statement::class), Expect::string())->default(TypeResolver::class),
			'types' => Expect::arrayOf(
				keyType: Expect::anyOf(Expect::int(), Expect::string()),
				valueType: Expect::anyOf(
					Expect::type(Statement::class),
					Expect::string(),
				),
			)->default([]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('tableManager'))
			->setFactory(TableManager::class);

		$builder->addDefinition($this->prefix('scaffolding'))
			->setFactory(TablesDefinitions::class);

		$builder->addDefinition($this->prefix('reflector'))
			->setFactory(PostgresReflector::class);

		$typeResolver = $builder->addDefinition($this->prefix('typeResolver'))
			->setType(TypeResolver::class)
			->setFactory($this->config->typeResolver);

		foreach ($this->config->types as $key => $type) {
			$type = $type instanceof Statement ? $type : new Statement($type);

			if (is_int($key)) {
				$typeResolver->addSetup('addResolutionByTypeName', [$type]);
			} else {
				$typeResolver->addSetup('addResolutionByLocation', [
					new Statement(Identifier::class, explode('.', $key)),
					$type,
				]);
			}
		}
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$typeResolver = $builder->getDefinition($this->prefix('typeResolver'));
		\assert($typeResolver instanceof ServiceDefinition);

		foreach ($builder->findByType(TypeResolverConfigurator::class) as $configurator) {
			$typeResolver->addSetup('?->configure(?)', [$configurator, '@self']);
		}
	}
}
