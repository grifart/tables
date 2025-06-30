<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\Tables\ColumnMetadata;
use Grifart\Tables\Modifications;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PromotedParameter;
use Nette\PhpGenerator\PropertyHookType;

final class ModificationsImplementation implements Capability
{

	private string $modificationsStorage = '$this->modifications';

	/**
	 * @param array<string, ColumnMetadata> $columnMetadata
	 */
	public function __construct(
		private string $relatedTableClass,
		private string $primaryKeyClass,
		private array $columnMetadata,
	)
	{
	}


	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$namespace = $draft->getNamespace();
		$classType = $draft->getClassType();

		$namespace->addUse(Modifications::class);
		$classType->addImplement(Modifications::class);
		$classType->addComment(\sprintf('@implements Modifications<%s>', $namespace->simplifyName($this->relatedTableClass)));

		$classType->addProperty('modifications')
			->setVisibility('public', 'private')
			->setType('array')
			->addComment('@var array<string, mixed>')
			->setValue([]);

		$classType->addMethod('__construct')
			->setVisibility('private')
			->setParameters([
				new PromotedParameter('primaryKey')
					->setType($this->primaryKeyClass)
					->setNullable()
					->setDefaultValue(null)
					->setVisibility('public')
					->setReadOnly(),
			]);

		// ::update() constructor
		$namespace->addUse($this->primaryKeyClass);
		$classType->addMethod('update')
			->setStatic()
			->setVisibility('public')
			->setReturnType('self')
			->setParameters([
				(new Parameter('primaryKey'))
					->setType($this->primaryKeyClass)
			])
			->setBody('return new self($primaryKey);');

		$classType->addMethod('new')
			->setStatic()
			->setVisibility('public')
			->setReturnType('self')
			->setBody('return new self();');

		// implement forTable method
		$namespace->addUse($this->relatedTableClass);
		$classType->addMethod('forTable')
			->setStatic()
			->setVisibility('public')
			->setReturnType('string')
			->setBody('return ?::class;', [
				new Literal($namespace->simplifyName($this->relatedTableClass))
			]);

		// modify*() methods
		foreach ($definition->getFields() as $field) {
			$fieldName = $field->getName();
			$type = $field->getType();

			if ($this->columnMetadata[$fieldName]->isGenerated()) {
				continue;
			}

			$property = $classType->addProperty($fieldName)
				->setType($type->getTypeHint())
				->setNullable($type->isNullable());

			// add getter
			$modifier = $classType->addMethod('modify' . \ucfirst($fieldName))
				->setVisibility('public')
				->addBody('?[?] = ?;', [
					new Literal($this->modificationsStorage),
					$fieldName,
					new Literal('$' . $fieldName),
				])
				->setParameters([
					(new Parameter($fieldName))
						->setType($type->getTypeHint())
						->setNullable($type->isNullable())
				])
				->setReturnType('void')
				->addAttribute(\Deprecated::class, ['Use $' . $fieldName . ' property instead.']);

			// add phpDoc type hints if necessary
			if ($type->requiresDocComment()) {
				$docCommentType = $type->getDocCommentType($namespace);
				$property->addComment(\sprintf('@var %s', $docCommentType));
				$modifier->addComment(\sprintf('@param %s $%s', $docCommentType, $fieldName));
			}

			$property->addHook(PropertyHookType::Set)
				->setBody(
					'?[?] = $value;',
					[
						new Literal($this->modificationsStorage),
						$fieldName,
					],
				);
		}
	}
}
