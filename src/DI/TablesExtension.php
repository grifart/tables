<?php

declare(strict_types=1);

namespace Grifart\Tables\DI;

use Grifart\Tables\Scaffolding\PostgresReflector;
use Grifart\Tables\Scaffolding\TablesDefinitions;
use Grifart\Tables\TableManager;
use Grifart\Tables\TypeResolver;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

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
				Expect::anyOf(
					Expect::type(Statement::class),
					Expect::string(),
					Expect::structure([
						'type' => Expect::anyOf(Expect::type(Statement::class), Expect::string()),
						'location' => Expect::type(Statement::class),
					]),
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

		foreach ($this->config->types as $typeDefinition) {
			if ($typeDefinition instanceof \stdClass) {
				$type = $typeDefinition->type instanceof Statement ? $typeDefinition->type : new Statement($typeDefinition->type);
				$typeResolver->addSetup('addResolutionByLocation', [$typeDefinition->location, $type]);

			} elseif (\is_string($typeDefinition)) {
				$typeResolver->addSetup('addResolutionByTypeName', [new Statement($typeDefinition)]);

			} elseif ($typeDefinition instanceof Statement) {
				$typeResolver->addSetup('addResolutionByTypeName', [$typeDefinition]);
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
