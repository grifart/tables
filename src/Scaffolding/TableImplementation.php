<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;


use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\ClassScaffolder\Definition\Types\UnionType;
use Grifart\Tables\CaseConversion;
use Grifart\Tables\Column;
use Grifart\Tables\ColumnMetadata;
use Grifart\Tables\ColumnNotFound;
use Grifart\Tables\Conditions\Condition;
use Grifart\Tables\DefaultValue;
use Grifart\Tables\Expression;
use Grifart\Tables\GivenSearchCriteriaHaveNotMatchedAnyRows;
use Grifart\Tables\RowNotFound;
use Grifart\Tables\OrderBy\OrderBy;
use Grifart\Tables\RowWithGivenPrimaryKeyAlreadyExists;
use Grifart\Tables\Table;
use Grifart\Tables\TableManager;
use Grifart\Tables\TooManyRowsFound;
use Grifart\Tables\Type;
use Grifart\Tables\TypeResolver;
use Grifart\Tables\UnchangedValue;
use Nette\PhpGenerator as Code;
use Nette\Utils\Paginator;
use function Grifart\ClassScaffolder\Definition\Types\resolve;
use function in_array;
use function usort;

final class TableImplementation implements Capability
{
	/**
	 * @param array<string, ColumnMetadata> $columnMetadata
	 * @param array<string, PhpType> $columnPhpTypes
	 */
	public function __construct(
		private string $schema,
		private string $tableName,
		private string $primaryKeyClass,
		private string $rowClass,
		private string $modificationClass,
		private array $columnMetadata,
		private array $columnPhpTypes,
	) {}

	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$namespace = $draft->getNamespace();
		$classType = $draft->getClassType();

		// implements table
		$namespace->addUse(Table::class);
		$classType->addImplement(Table::class);

		// config methods:
		$this->implementConfigMethod($classType, 'getSchema', $this->schema);
		$this->implementConfigMethod($classType, 'getTableName', $this->tableName);

		$this->implementConfigMethodReturningClass($namespace, $classType, 'getPrimaryKeyClass', $this->primaryKeyClass);
		$this->implementConfigMethodReturningClass($namespace, $classType, 'getRowClass', $this->rowClass);
		$this->implementConfigMethodReturningClass($namespace, $classType, 'getModificationClass', $this->modificationClass);

		// column info:
		$namespace->addUse(ColumnMetadata::class);
		$columnsDefinitions = []; // name => Literal
		$columnsArrayTemplate = [];
		foreach($this->columnMetadata as $column) {
			$columnsArrayTemplate[] = "\t? => new ColumnMetadata(?, ?, ?, ?, ?)";
			$columnsDefinitions[] = $column->getName();
			$columnsDefinitions[] = $column->getName();
			$columnsDefinitions[] = $column->getType();
			$columnsDefinitions[] = $column->isNullable();
			$columnsDefinitions[] = $column->hasDefaultValue();
			$columnsDefinitions[] = $column->isGenerated();
		}
		$columnsArrayTemplate = \implode(",\n", $columnsArrayTemplate);

		$classType->addMethod('getDatabaseColumns')
			->setReturnType('array')
			->addComment("@return ColumnMetadata[]")
			->setStatic()
			->setBody("return [\n".$columnsArrayTemplate."\n];", $columnsDefinitions)
			->addAttribute(\Override::class);


		$classType->addMethod('find')
			->setParameters([
				(new Code\Parameter('primaryKey'))
					->setType($this->primaryKeyClass)
			])
			->setReturnType($this->rowClass)
			->setReturnNullable()
			->addBody('$row = $this->tableManager->find($this, $primaryKey, required: false);')
			->addBody('\assert($row instanceof ? || $row === null);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');

		$namespace->addUse(RowNotFound::class);
		$classType->addMethod('get')
			->setParameters([
				(new Code\Parameter('primaryKey'))
					->setType($this->primaryKeyClass)
			])
			->setReturnType($this->rowClass)
			->addComment('@throws RowNotFound')
			->addBody('$row = $this->tableManager->find($this, $primaryKey, required: true);')
			->addBody('\assert($row instanceof ?);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');

		$namespace->addUse(OrderBy::class);
		$namespace->addUse(Paginator::class);
		$classType->addMethod('getAll')
			->addComment('@param OrderBy[] $orderBy')
			->addComment('@return ' . $namespace->simplifyName($this->rowClass) . '[]')
			->setParameters([
				(new Code\Parameter('orderBy'))->setType('array')->setDefaultValue([]),
				(new Code\Parameter('paginator'))->setType(Paginator::class)->setNullable()->setDefaultValue(null),
			])
			->setReturnType('array')
			->setBody(
				"/** @var ?[] \$result */\n" .
				"\$result = \$this->tableManager->getAll(\$this, \$orderBy, \$paginator);\n" .
				'return $result;',
				[new Code\Literal($namespace->simplifyName($this->rowClass))],
			);

		$namespace->addUse(Condition::class);
		$namespace->addUse(Expression::class);

		$classType->addMethod('findBy')
			->setParameters([
				(new Code\Parameter('conditions'))->setType(Condition::class . '|array'),
				(new Code\Parameter('orderBy'))->setType('array')->setDefaultValue([]),
				(new Code\Parameter('paginator'))->setType(Paginator::class)->setNullable()->setDefaultValue(null),
			])
			->addComment('@param Condition|Condition[] $conditions')
			->addComment('@param array<OrderBy|Expression<mixed>> $orderBy')
			->addComment('@return ' . $namespace->simplifyName($this->rowClass) . '[]')
			->setReturnType('array')
			->addBody('/** @var ?[] $result */', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('$result = $this->tableManager->findBy($this, $conditions, $orderBy, $paginator);')
			->addBody('return $result;');

		$classType->addMethod('count')
			->setParameters([
				(new Code\Parameter('conditions'))
					->setType(Condition::class . '|array')
					->setDefaultValue([]),
			])
			->addComment('@param Condition|Condition[] $conditions')
			->setReturnType('int')
			->setBody('return $this->tableManager->count($this, $conditions);');

		$namespace->addUse(TooManyRowsFound::class);
		$classType->addMethod('getUniqueBy')
			->setParameters([
				(new Code\Parameter('conditions'))->setType(Condition::class . '|array'),
			])
			->addComment('@param Condition|Condition[] $conditions')
			->addComment('@return ' . $namespace->simplifyName($this->rowClass))
			->addComment('@throws RowNotFound')
			->setReturnType($this->rowClass)
			->addBody('$row = $this->tableManager->findOneBy($this, $conditions, required: true, unique: true);')
			->addBody('\assert($row instanceof ?);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');

		$classType->addMethod('findUniqueBy')
			->setParameters([
				(new Code\Parameter('conditions'))->setType(Condition::class . '|array'),
			])
			->addComment('@param Condition|Condition[] $conditions')
			->addComment('@return ' . $namespace->simplifyName($this->rowClass) . '|null')
			->addComment('@throws RowNotFound')
			->setReturnType($this->rowClass)
			->setReturnNullable()
			->addBody('$row = $this->tableManager->findOneBy($this, $conditions, required: false, unique: true);')
			->addBody('\assert($row instanceof ? || $row === null);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');

		$classType->addMethod('getFirstBy')
			->setParameters([
				(new Code\Parameter('conditions'))->setType(Condition::class . '|array'),
				(new Code\Parameter('orderBy'))->setType('array')->setDefaultValue([]),
			])
			->addComment('@param Condition|Condition[] $conditions')
			->addComment('@param array<OrderBy|Expression<mixed>> $orderBy')
			->addComment('@return ' . $namespace->simplifyName($this->rowClass))
			->addComment('@throws RowNotFound')
			->setReturnType($this->rowClass)
			->addBody('$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: true, unique: false);')
			->addBody('\assert($row instanceof ?);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');

		$classType->addMethod('findFirstBy')
			->setParameters([
				(new Code\Parameter('conditions'))->setType(Condition::class . '|array'),
				(new Code\Parameter('orderBy'))->setType('array')->setDefaultValue([]),
			])
			->addComment('@param Condition|Condition[] $conditions')
			->addComment('@param array<OrderBy|Expression<mixed>> $orderBy')
			->addComment('@return ' . $namespace->simplifyName($this->rowClass) . '|null')
			->setReturnType($this->rowClass)
			->setReturnNullable()
			->addBody('$row = $this->tableManager->findOneBy($this, $conditions, $orderBy, required: false, unique: false);')
			->addBody('\assert($row instanceof ? || $row === null);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');


		$namespace->addUse(RowWithGivenPrimaryKeyAlreadyExists::class);
		$namespace->addUse(GivenSearchCriteriaHaveNotMatchedAnyRows::class);

		$classType->addMethod('save')
			->addComment('@throws RowWithGivenPrimaryKeyAlreadyExists')
			->addComment('@throws GivenSearchCriteriaHaveNotMatchedAnyRows')
			->setReturnType('void')
			->setParameters([
				(new Code\Parameter('changes'))->setType($this->modificationClass)
			])
			->setBody(
				'$this->tableManager->save($this, $changes);'
			);

		$newMethod = $classType->addMethod('new')
			->setReturnType($this->modificationClass)
			->addBody('$modifications = ?::new();', [new Code\Literal($namespace->simplifyName($this->modificationClass))]);

		$editMethod = $classType->addMethod('edit')
			->setReturnType($this->modificationClass)
			->setParameters([
				(new Code\Parameter('rowOrKey'))->setType($this->rowClass . '|' . $this->primaryKeyClass),
			])
			->addBody('$primaryKey = $rowOrKey instanceof ? \? $rowOrKey : ?::fromRow($rowOrKey);', [
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
			])
			->addBody('$modifications = ?::update($primaryKey);', [new Code\Literal($namespace->simplifyName($this->modificationClass))]);

		$insertMethod = $classType->addMethod('insert')
			->addComment('@throws RowWithGivenPrimaryKeyAlreadyExists')
			->setReturnType('void')
			->addBody('$modifications = ?::new();', [new Code\Literal($namespace->simplifyName($this->modificationClass))]);

		$insertAndGetMethod = $classType->addMethod('insertAndGet')
			->addComment('@throws RowWithGivenPrimaryKeyAlreadyExists')
			->setReturnType($this->rowClass)
			->addBody('$modifications = ?::new();', [new Code\Literal($namespace->simplifyName($this->modificationClass))]);

		$updateMethod = $classType->addMethod('update')
			->addComment('@throws GivenSearchCriteriaHaveNotMatchedAnyRows')
			->setReturnType('void')
			->setParameters([
				(new Code\Parameter('rowOrKey'))->setType($this->rowClass . '|' . $this->primaryKeyClass),
			])
			->addBody('$primaryKey = $rowOrKey instanceof ? \? $rowOrKey : ?::fromRow($rowOrKey);', [
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
			])
			->addBody('$modifications = ?::update($primaryKey);', [new Code\Literal($namespace->simplifyName($this->modificationClass))]);

		$updateAndGetMethod = $classType->addMethod('updateAndGet')
			->addComment('@throws GivenSearchCriteriaHaveNotMatchedAnyRows')
			->setReturnType($this->rowClass)
			->setParameters([
				(new Code\Parameter('rowOrKey'))->setType($this->rowClass . '|' . $this->primaryKeyClass),
			])
			->addBody('$primaryKey = $rowOrKey instanceof ? \? $rowOrKey : ?::fromRow($rowOrKey);', [
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
			])
			->addBody('$modifications = ?::update($primaryKey);', [new Code\Literal($namespace->simplifyName($this->modificationClass))]);

		$updateByMethod = $classType->addMethod('updateBy')
			->setReturnType('void')
			->setParameters([
				(new Code\Parameter('conditions'))->setType(Condition::class . '|array'),
			])
			->addComment('@param Condition|Condition[] $conditions')
			->addBody('$modifications = ?::new();', [new Code\Literal($namespace->simplifyName($this->modificationClass))]);

		$upsertMethod = $classType->addMethod('upsert')
			->setReturnType('void')
			->addBody('$modifications = ?::new();', [new Code\Literal($namespace->simplifyName($this->modificationClass))]);

		$upsertAndGetMethod = $classType->addMethod('upsertAndGet')
			->setReturnType($this->rowClass)
			->addBody('$modifications = ?::new();', [new Code\Literal($namespace->simplifyName($this->modificationClass))]);

		$classType->addMethod('delete')
			->setReturnType('void')
			->setParameters([
				(new Code\Parameter('rowOrKey'))->setType($this->rowClass . '|' . $this->primaryKeyClass)
			])
			->addBody('$primaryKey = $rowOrKey instanceof ? \? $rowOrKey : ?::fromRow($rowOrKey);', [
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
			])
			->addBody('$this->tableManager->delete($this, $primaryKey);');

		$classType->addMethod('deleteAndGet')
			->setReturnType($this->rowClass)
			->setParameters([
				(new Code\Parameter('rowOrKey'))->setType($this->rowClass . '|' . $this->primaryKeyClass)
			])
			->addBody('$primaryKey = $rowOrKey instanceof ? \? $rowOrKey : ?::fromRow($rowOrKey);', [
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
				new Code\Literal($namespace->simplifyName($this->primaryKeyClass)),
			])
			->addBody('$row = $this->tableManager->deleteAndGet($this, $primaryKey);')
			->addBody('\assert($row instanceof ?);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');

		$classType->addMethod('deleteBy')
			->setReturnType('void')
			->setParameters([
				(new Code\Parameter('conditions'))->setType(Condition::class . '|array'),
			])
			->addComment('@param Condition|Condition[] $conditions')
			->addBody('$this->tableManager->deleteBy($this, $conditions);');

		$columns = $this->columnMetadata;
		usort($columns, fn (ColumnMetadata $a, ColumnMetadata $b) => $a->hasDefaultValue() <=> $b->hasDefaultValue());

		$methods = [$insertMethod, $insertAndGetMethod, $updateMethod, $updateAndGetMethod, $updateByMethod, $upsertMethod, $upsertAndGetMethod, $newMethod, $editMethod];
		foreach ($methods as $method) {
			foreach ($columns as $columnMetadata) {
				$isEditMethod = in_array($method, [$updateMethod, $updateAndGetMethod, $updateByMethod, $editMethod], true);
				$hasDefaultValue = $columnMetadata->hasDefaultValue();

				$isGenerated = $columnMetadata->isGenerated();
				if ($isGenerated) {
					continue;
				}

				$fieldName = $columnMetadata->getName();
				$fieldType = $this->columnPhpTypes[$fieldName];
				$isNullable = $fieldType->isNullable();

				$namespace->addUse(DefaultValue::class);
				$namespace->addUse('Grifart\Tables\DefaultValue', of: $namespace::NameConstant);

				$namespace->addUse(UnchangedValue::class);
				$namespace->addUse('Grifart\Tables\Unchanged', of: $namespace::NameConstant);

				if (($hasDefaultValue || $isEditMethod) && $fieldType->getTypeHint() !== 'mixed') {
					$types = [$fieldType];

					if ($isEditMethod) {
						$types[] = resolve(UnchangedValue::class);
					}

					if ($hasDefaultValue) {
						$types[] = resolve(DefaultValue::class);
					}

					$fieldType = new UnionType(...$types);
				}


				$parameter = $method->addParameter($fieldName)
					->setType($fieldType->getTypeHint())
					->setNullable($isNullable);

				if ($hasDefaultValue || $isEditMethod) {
					$parameter->setDefaultValue(
						new Code\Literal(
							$namespace->simplifyName(
								$isEditMethod ? 'Grifart\Tables\Unchanged' : 'Grifart\Tables\DefaultValue',
								$namespace::NameConstant,
							),
						),
					);
				}

				if ($fieldType->requiresDocComment()) {
					$method->addComment(\sprintf(
						'@param %s $%s',
						$fieldType->getDocCommentType($namespace),
						$fieldName,
					));
				}

				if ($hasDefaultValue || $isEditMethod) {
					$method->addBody(
						'if (!? instanceof ?) {',
						[
							new Code\Literal('$' . $fieldName),
							new Code\Literal($namespace->simplifyName($isEditMethod ? UnchangedValue::class : DefaultValue::class)),
						],
					);
				}

				$method->addBody(
					(($hasDefaultValue || $isEditMethod) ? "\t" : '') . '$modifications->' . $fieldName . ' = ?;',
					[new Code\Literal('$' . $fieldName)],
				);

				if ($hasDefaultValue || $isEditMethod) {
					$method->addBody('}');
				}
			}
		}

		$newMethod->addBody('return $modifications;');
		$editMethod->addBody('return $modifications;');

		$insertMethod
			->addBody('$this->tableManager->insert($this, $modifications);');

		$insertAndGetMethod
			->addBody('$row = $this->tableManager->insertAndGet($this, $modifications);')
			->addBody('\assert($row instanceof ?);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');

		$updateMethod
			->addBody('$this->tableManager->update($this, $modifications);');

		$updateAndGetMethod
			->addBody('$row = $this->tableManager->updateAndGet($this, $modifications);')
			->addBody('\assert($row instanceof ?);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');

		$updateByMethod
			->addBody('$this->tableManager->updateBy($this, $conditions, $modifications);');

		$upsertMethod
			->addBody('$this->tableManager->upsert($this, $modifications);');

		$upsertAndGetMethod
			->addBody('$row = $this->tableManager->upsertAndGet($this, $modifications);')
			->addBody('\assert($row instanceof ?);', [new Code\Literal($namespace->simplifyName($this->rowClass))])
			->addBody('return $row;');

		$namespace->addUse(TableManager::class);
		$namespace->addUse(TypeResolver::class);
		$constructor = $classType->addMethod('__construct');
		$constructor->addPromotedParameter('tableManager')->setType(TableManager::class)->setPrivate();
		$constructor->addPromotedParameter('typeResolver')->setType(TypeResolver::class)->setPrivate();





		// add column constants and accessors

		$namespace->addUse(Column::class);

		$columnsProperty = $classType->addProperty('columns')
			->setPrivate()
			->setType('array');

		$columnsShape = [];
		$columnInitializers = [];

		foreach ($this->columnMetadata as $columnInfo) {
			$columnName = $columnInfo->getName();
			$docCommentType = $this->columnPhpTypes[$columnName]->getDocCommentType($namespace);

			$classType->addConstant(CaseConversion::toUnderscores($columnName), $columnName)
				->setType('string')
				->setPublic();

			$classType->addMethod($columnName)
				->setReturnType(Column::class)
				->addComment(\sprintf('@return Column<self, %s>', $docCommentType))
				->addBody('return $this->columns[?];', [$columnName])
				->addAttribute(\Deprecated::class, [\sprintf('Use $%s property instead', $columnName)]);

			$classType->addProperty($columnName)
				->setType(Column::class)
				->addComment(\sprintf('@var Column<self, %s>', $docCommentType))
				->addHook(Code\PropertyHookType::Get)
				->setBody('$this->columns[?]', [$columnName], short: true);

			$columnsShape[] = \sprintf('%s: Column<self, %s>', $columnName, $docCommentType);

			$constructor->addBody(\sprintf('/** @var Column<self, %s> $%s */', $docCommentType, $columnName));
			$constructor->addBody('$? = Column::from($this, self::getDatabaseColumns()[?], $this->typeResolver);', [$columnName, $columnName]);
			$columnInitializers[$columnName] = new Code\Literal('$?', [$columnName]);
		}

		$columnsProperty->addComment(\sprintf('@var array{%s}', \implode(', ', $columnsShape)));
		$constructor->addBody('$this->columns = ?;', [$columnInitializers]);

		$namespace->addUse(Type::class);
		$namespace->addUse(ColumnNotFound::class);
		$getTypeOf = $classType->addMethod('getTypeOf')
			->setPublic()
			->setReturnType(Type::class)
			->addAttribute(\Override::class);

		$getTypeOf->addParameter('columnName')->setType('string');
		$getTypeOf->addBody('$column = $this->columns[$columnName] ?? throw ColumnNotFound::of($columnName, \get_class($this));');
		$getTypeOf->addBody('/** @var Type<mixed> $type */');
		$getTypeOf->addBody('$type = $column->getType();');
		$getTypeOf->addBody('return $type;');
		$getTypeOf->addComment('@internal');
		$getTypeOf->addComment('@return Type<mixed>');
	}

	private function implementConfigMethodReturningClass(Code\PhpNamespace $namespace, Code\ClassType $classType, string $name, string $class): void
	{
		$namespace->addUse($class);
		$this->implementConfigMethod($classType, $name, new Code\Literal($namespace->simplifyName($class) . '::class'));
	}

	private function implementConfigMethod(Code\ClassType $classType, string $name, mixed $value): void
	{
		$classType->addMethod($name)
			->setStatic()
			->setReturnType('string')
			->setBody('return ?;', [
				$value
			])
			->addAttribute(\Override::class);
	}
}
