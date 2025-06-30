<?php

declare(strict_types=1);

namespace Grifart\Tables\Scaffolding;

use Dibi\IConnection;
use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\Tables\SingleConnectionTableManager;
use Grifart\Tables\TableFactory;
use Grifart\Tables\TableManager;
use Grifart\Tables\TypeResolver;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PromotedParameter;

final readonly class TableFactoryImplementation implements Capability
{
	public function __construct(
		private string $tableClass,
	) {}

	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$namespace = $draft->getNamespace();
		$classType = $draft->getClassType();

		$classType->addImplement(TableFactory::class);
		$classType->setReadOnly();

		$namespace->addUse(TypeResolver::class);
		$namespace->addUse(TableManager::class);

		$classType->addMethod('__construct')
			->setPublic()
			->setParameters([
				(new PromotedParameter('tableManager'))
					->setPrivate()
					->setType(TableManager::class),
				(new PromotedParameter('typeResolver'))
					->setPrivate()
					->setType(TypeResolver::class),
			]);

		$namespace->addUse($this->tableClass);

		$classType->addMethod('create')
			->setPublic()
			->setReturnType($this->tableClass)
			->setBody('return new ?($this->tableManager, $this->typeResolver);', [new Literal($namespace->simplifyName($this->tableClass))])
			->addAttribute(\Override::class);

		$classType->addMethod('withTableManager')
			->setPublic()
			->setReturnType($this->tableClass)
			->setParameters([(new Parameter('tableManager'))->setType(TableManager::class)])
			->setBody('return new ?($tableManager, $this->typeResolver);', [new Literal($namespace->simplifyName($this->tableClass))])
			->addAttribute(\Override::class);

		$namespace->addUse(IConnection::class);
		$namespace->addUse(SingleConnectionTableManager::class);
		$classType->addMethod('withConnection')
			->setPublic()
			->setReturnType($this->tableClass)
			->setParameters([(new Parameter('connection'))->setType(IConnection::class)])
			->addBody('$tableManager = new ?($connection);', [new Literal($namespace->simplifyName(SingleConnectionTableManager::class))])
			->addBody('return new ?($tableManager, $this->typeResolver);', [new Literal($namespace->simplifyName($this->tableClass))])
			->addAttribute(\Override::class);
	}
}
