<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\Tables\ColumnMetadata;
use Grifart\Tables\Modifications;
use Grifart\Tables\ModificationsTrait;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpLiteral;

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

		$namespace->addUse(ModificationsTrait::class);
		$classType->addTrait(ModificationsTrait::class, true)
			->addComment(\sprintf('@use ModificationsTrait<%s>', $namespace->simplifyName($this->relatedTableClass)));

		$namespace->addUse(Modifications::class);
		$classType->addImplement(Modifications::class);
		$classType->addComment(\sprintf('@implements Modifications<%s>', $namespace->simplifyName($this->relatedTableClass)));

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
			->setBody('return self::_update($primaryKey);');

		$classType->addMethod('new')
			->setStatic()
			->setVisibility('public')
			->setReturnType('self')
			->setBody('return self::_new();');

		// implement forTable method
		$namespace->addUse($this->relatedTableClass);
		$classType->addMethod('forTable')
			->setStatic()
			->setVisibility('public')
			->setReturnType('string')
			->setBody('return ?::class;', [
				new PhpLiteral($namespace->simplifyName($this->relatedTableClass))
			]);

		// modify*() methods
		foreach ($definition->getFields() as $field) {
			$fieldName = $field->getName();
			$type = $field->getType();

			if ($this->columnMetadata[$fieldName]->isGenerated()) {
				continue;
			}

			// add getter
			$modifier = $classType->addMethod('modify' . \ucfirst($fieldName))
				->setVisibility('public')
				->addBody('?[?] = ?;', [
					new PhpLiteral($this->modificationsStorage),
					$fieldName,
					new PhpLiteral('$' . $fieldName),
				])
				->setParameters([
					(new Parameter($fieldName))
						->setType($type->getTypeHint())
						->setNullable($type->isNullable())
				]);
			$modifier->setReturnType('void');


			// add phpDoc type hints if necessary
			if ($type->requiresDocComment()) {
				$docCommentType = $type->getDocCommentType($namespace);

				$modifier->addComment(\sprintf(
					'@param %s $%s',
					$docCommentType,
					$fieldName,
				));
			}
		}
	}
}
