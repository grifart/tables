<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;


use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types\Type;
use Grifart\Tables\CaseConvertion;
use Grifart\Tables\RowNotFound;
use Grifart\Tables\Table;
use Grifart\Tables\TableManager;
use Nette\PhpGenerator as Code;
use Webmozart\Assert\Assert;

final class TableDecorator implements ClassDecorator
{

	private string $schema;

	private string $tableName;

	private string $primaryKeyClass;

	private string $rowClass;

	private string $modificationClass;

	/** @var Column[] */
	private array $columnInfo;

	/** @var array<string, Type> */
	private array $columnPhpTypes;

	/**
	 * @param array<string, Column> $columnInfo
	 * @param array<string, Type> $columnPhpTypes
	 */
	public function __construct(string $schema, string $tableName, string $primaryKeyClass, string $rowClass, string $modificationClass, array $columnInfo, array $columnPhpTypes)
	{
		$this->schema = $schema;
		$this->tableName = $tableName;

		$this->primaryKeyClass = $primaryKeyClass;
		$this->rowClass = $rowClass;
		$this->modificationClass = $modificationClass;

		Assert::allIsInstanceOf($columnInfo, Column::class);
		$this->columnInfo = $columnInfo;
		$this->columnPhpTypes = $columnPhpTypes;
	}


	public function decorate(Code\PhpNamespace $namespace, Code\ClassType $classType, ClassDefinition $definition): void
	{
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
		$namespace->addUse(Column::class);
		$columnsDefinitions = []; // name => PhpLiteral
		$columnsArrayTemplate = [];
		foreach($this->columnInfo as $column) {
			$columnsArrayTemplate[] = "\t? => new Column(?, ?, ?, ?)";
			$columnsDefinitions[] = $column->getName();
			$columnsDefinitions[] = $column->getName();
			$columnsDefinitions[] = $column->getType();
			$columnsDefinitions[] = $column->isNullable();
			$columnsDefinitions[] = $column->hasDefaultValue();
		}
		$columnsArrayTemplate = \implode(",\n", $columnsArrayTemplate);

		$classType->addMethod('getDatabaseColumns')
			->setReturnType('array')
			->addComment("@return Column[]")
			->setStatic()
			->setBody("return [\n".$columnsArrayTemplate."\n];", $columnsDefinitions);


		// Column references
		// todo add - use constants? Or references to Column class?

		$namespace->addUse(TableManager::class);
		$classType->addProperty('tableManager')
			->addComment('@var TableManager')
			->setVisibility('private');

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
				[new Code\PhpLiteral($namespace->unresolveName($this->rowClass))]
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
			->setComment('@return ' . $namespace->unresolveName($this->rowClass) . '[]')
			->setReturnType('array')
			->setBody(
				'/** @var ?[] $result */' . "\n" .
				'$result = $this->tableManager->findBy($this, $conditions);' . "\n" .
				'return $result;',
				[
					new Code\PhpLiteral($namespace->unresolveName($this->rowClass))
				]
			);

		$classType->addMethod('newEmpty')
			->setReturnType($this->modificationClass)
			->setBody(
				'return ?::new();',
				[new Code\PhpLiteral($namespace->unresolveName($this->modificationClass))],
			);

		$newMethod = $classType->addMethod('new')
			->setReturnType($this->modificationClass)
			->addBody(
				'$modifications = ?::new();',
				[new Code\PhpLiteral($namespace->unresolveName($this->modificationClass))],
			);

		foreach ($this->columnInfo as $columnInfo) {
			if ( ! $columnInfo->hasDefaultValue()) {
				$fieldName = $columnInfo->getName();
				$fieldType = $this->columnPhpTypes[$fieldName];

				$newMethod->addParameter($fieldName)
					->setTypeHint($fieldType->getTypeHint())
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
				(new Code\Parameter('row'))->setTypeHint($this->rowClass)
			])
			->setBody(
				'/** @var ? $primaryKeyClass */' . "\n" .
				'$primaryKeyClass = self::getPrimaryKeyClass();' . "\n" .
				"\n" .

				'return ?::update(' . "\n" .
				"\t" . '$primaryKeyClass::fromRow($row)' . "\n" .
				');',
				[
					new Code\PhpLiteral($namespace->unresolveName($this->primaryKeyClass)),
					new Code\PhpLiteral($namespace->unresolveName($this->modificationClass)),
				]
			);

		$classType->addMethod('editByKey')
			->setReturnType($this->modificationClass)
			->setParameters([
				(new Code\Parameter('primaryKey'))->setTypeHint($this->primaryKeyClass)
			])
			->setBody(
				'return ?::update($primaryKey);',
				[
					new Code\PhpLiteral($namespace->unresolveName($this->modificationClass)),
				]
			);


		$classType->addMethod('save')
			->setReturnType('void')
			->setParameters([
				(new Code\Parameter('changes'))->setTypeHint($this->modificationClass)
			])
			->setBody(
				'$this->tableManager->save($this, $changes);'
			);

		$classType->addMethod('delete')
			->setReturnType('void')
			->setParameters([
				(new Code\Parameter('primaryKey'))->setTypeHint($this->primaryKeyClass)
			])
			->setBody(
				'$this->tableManager->delete($this, $primaryKey);'
			);

		$classType->addMethod('__construct')
			->setParameters([
				(new Code\Parameter('tableManager'))->setTypeHint(TableManager::class)
			])
			->setBody(
				'$this->tableManager = $tableManager;'
			);





		// add column constants

		foreach ($this->columnInfo as $columnInfo) {
			$classType->addConstant(
				CaseConvertion::toUnderscores($columnInfo->getName()),
				$columnInfo->getName()
			)->setVisibility('public');

		}
	}

	private function implementConfigMethodReturningClass(Code\PhpNamespace $namespace, Code\ClassType $classType, string $name, string $class): void
	{
		$namespace->addUse($class);
		$this->implementConfigMethod($classType, $name, new Code\PhpLiteral($namespace->unresolveName($class) . '::class'));
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
