<?php

declare(strict_types=1);

namespace Grifart\Tables\DI;

use Grifart\Tables\TableManager;
use Grifart\Tables\TypeResolver;
use Nette\DI\CompilerExtension;
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
			'types' => Expect::structure([
				'byName' => Expect::arrayOf(
					valueType: Expect::anyOf(Expect::type(Statement::class), Expect::string()),
					keyType: Expect::string(),
				)->default([]),
				'byLocation' => Expect::arrayOf(
					valueType: Expect::anyOf(Expect::type(Statement::class), Expect::string()),
					keyType: Expect::string(),
				)->default([]),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('tableManager'))
			->setFactory(TableManager::class);

		$typeResolver = $builder->addDefinition($this->prefix('typeResolver'))
			->setType(TypeResolver::class)
			->setFactory($this->config->typeResolver);

		foreach ($this->config->types->byName as $typeName => $type) {
			$typeResolver->addSetup('addResolutionByTypeName', [$typeName, $type instanceof Statement ? $type : new Statement($type)]);
		}

		foreach ($this->config->types->byLocation as $location => $type) {
			$typeResolver->addSetup('addResolutionByLocation', [$location, $type instanceof Statement ? $type : new Statement($type)]);
		}
	}
}
