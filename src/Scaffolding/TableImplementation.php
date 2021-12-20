<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;


use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\CaseConversion;
use Grifart\Tables\Column;
use Grifart\Tables\ColumnMetadata;
use Grifart\Tables\RowNotFound;
use Grifart\Tables\Table;
use Grifart\Tables\TableManager;
use Grifart\Tables\Type;
use Grifart\Tables\TypeResolver;
use Nette\PhpGenerator as Code;
use function Functional\map;

final class TableImplementation implements Capability
{
	/**
	 * @param array<string, ColumnMetadata> $columnMetadata
	 * @param array<string, Type<mixed>> $resolvedColumnTypes
	 * @param array<string, PhpType> $columnPhpTypes
	 */
	public function __construct(
		private string $schema,
		private string $tableName,
		private string $primaryKeyClass,
		private string $rowClass,
		private string $modificationClass,
		private array $columnMetadata,
		private array $resolvedColumnTypes,
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
		$columnsDefinitions = []; // name => PhpLiteral
		$columnsArrayTemplate = [];
		foreach($this->columnMetadata as $column) {
			$columnsArrayTemplate[] = "\t? => new ColumnMetadata(?, ?, ?, ?)";
			$columnsDefinitions[] = $column->getName();
			$columnsDefinitions[] = $column->getName();
			$columnsDefinitions[] = $column->getType();
			$columnsDefinitions[] = $column->isNullable();
			$columnsDefinitions[] = $column->hasDefaultValue();
		}
		$columnsArrayTemplate = \implode(",\n", $columnsArrayTemplate);

		$classType->addMethod('getDatabaseColumns')
			->setReturnType('array')
			->addComment("@return ColumnMetadata[]")
			->setStatic()
			->setBody("return [\n".$columnsArrayTemplate."\n];", $columnsDefinitions);


		// Column references
		// todo add - use constants? Or references to Column class?

		$classType->addMethod('find')
			->setParameters([
				(new Code\Parameter('primaryKey'))
					->setType($this->primaryKeyClass)
			])
			->setReturnType($this->rowClass)
			->setReturnNullable()
			->setBody(
				'$row = $this->tableManager->find($this, $primaryKey);' . "\n" .
				'\assert($row instanceof ? || $row === NULL);' . "\n" .
				'return $row;',
				[new Code\PhpLiteral($namespace->simplifyName($this->rowClass))]
			);

		$namespace->addUse(RowNotFound::class);
		$classType->addMethod('get')
			->setParameters([
				(new Code\Parameter('primaryKey'))
					->setType($this->primaryKeyClass)
			])
			->setReturnType($this->rowClass)
			->addComment('@throws RowNotFound')
			->setBody(
				'$row = $this->find($primaryKey);' . "\n" .
				'if ($row === NULL) {' . "\n" .
				'	throw new RowNotFound();' . "\n" .
				'}' . "\n" .
				'return $row;'
			);

		$classType->addMethod('findBy')
			->setParameters([
				(new Code\Parameter('conditions'))
					->setType('array')
			])
			->addComment('@param mixed[] $conditions')
			->addComment('@return ' . $namespace->simplifyName($this->rowClass) . '[]')
			->setReturnType('array')
			->setBody(
				'/** @var ?[] $result */' . "\n" .
				'$result = $this->tableManager->findBy($this, $conditions);' . "\n" .
				'return $result;',
				[
					new Code\PhpLiteral($namespace->simplifyName($this->rowClass))
				]
			);

		$classType->addMethod('newEmpty')
			->setReturnType($this->modificationClass)
			->setBody(
				'return ?::new();',
				[new Code\PhpLiteral($namespace->simplifyName($this->modificationClass))],
			);

		$newMethod = $classType->addMethod('new')
			->setReturnType($this->modificationClass)
			->addBody(
				'$modifications = ?::new();',
				[new Code\PhpLiteral($namespace->simplifyName($this->modificationClass))],
			);

		foreach ($this->columnMetadata as $columnMetadata) {
			if ( ! $columnMetadata->hasDefaultValue()) {
				$fieldName = $columnMetadata->getName();
				$fieldType = $this->columnPhpTypes[$fieldName];

				$newMethod->addParameter($fieldName)
					->setType($fieldType->getTypeHint())
					->setNullable($fieldType->isNullable());

				if ($fieldType->requiresDocComment()) {
					$newMethod->addComment(\sprintf(
						'@param %s $%s',
						$fieldType->getDocCommentType($namespace),
						$fieldName,
					));
				}

				$newMethod->addBody(
					'$modifications->modify' . \ucfirst($fieldName) . '(?);',
					[new Code\PhpLiteral('$' . $fieldName)],
				);
			}
		}

		$newMethod->addBody('return $modifications;');


		$classType->addMethod('edit')
			->setReturnType($this->modificationClass)
			->setParameters([
				(new Code\Parameter('row'))->setType($this->rowClass)
			])
			->setBody(
				'/** @var ? $primaryKeyClass */' . "\n" .
				'$primaryKeyClass = self::getPrimaryKeyClass();' . "\n" .
				"\n" .

				'return ?::update(' . "\n" .
				"\t" . '$primaryKeyClass::fromRow($row)' . "\n" .
				');',
				[
					new Code\PhpLiteral($namespace->simplifyName($this->primaryKeyClass)),
					new Code\PhpLiteral($namespace->simplifyName($this->modificationClass)),
				]
			);

		$classType->addMethod('editByKey')
			->setReturnType($this->modificationClass)
			->setParameters([
				(new Code\Parameter('primaryKey'))->setType($this->primaryKeyClass)
			])
			->setBody(
				'return ?::update($primaryKey);',
				[
					new Code\PhpLiteral($namespace->simplifyName($this->modificationClass)),
				]
			);


		$classType->addMethod('save')
			->setReturnType('void')
			->setParameters([
				(new Code\Parameter('changes'))->setType($this->modificationClass)
			])
			->setBody(
				'$this->tableManager->save($this, $changes);'
			);

		$classType->addMethod('delete')
			->setReturnType('void')
			->setParameters([
				(new Code\Parameter('primaryKey'))->setType($this->primaryKeyClass)
			])
			->setBody(
				'$this->tableManager->delete($this, $primaryKey);'
			);

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

			$classType->addConstant(CaseConversion::toUnderscores($columnName), $columnName)->setPublic();

			$classType->addMethod($columnName)
				->setReturnType(Column::class)
				->addComment(\sprintf('@return Column<self, %s>', $docCommentType))
				->addBody('return $this->columns[?];', [$columnName]);

			$columnsShape[] = \sprintf('%s: Column<self, %s>', $columnName, $docCommentType);

			$constructor->addBody(\sprintf('/** @var Column<self, %s> $%s */', $docCommentType, $columnName));
			$constructor->addBody('$? = Column::from($this, self::getDatabaseColumns()[?], $this->typeResolver);', [$columnName, $columnName]);
			$columnInitializers[$columnName] = new Code\PhpLiteral('$?', [$columnName]);
		}

		$columnsProperty->addComment(\sprintf('@var array{%s}', \implode(', ', $columnsShape)));
		$constructor->addBody('$this->columns = ?;', [$columnInitializers]);
	}

	private function implementConfigMethodReturningClass(Code\PhpNamespace $namespace, Code\ClassType $classType, string $name, string $class): void
	{
		$namespace->addUse($class);
		$this->implementConfigMethod($classType, $name, new Code\PhpLiteral($namespace->simplifyName($class) . '::class'));
	}

	private function implementConfigMethod(Code\ClassType $classType, string $name, mixed $value): void
	{
		$classType->addMethod($name)
			->setStatic()
			->setReturnType('string')
			->setBody('return ?;', [
				$value
			]);
	}
}
