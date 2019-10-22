<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;

use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\Tables\Modifications;
use Grifart\Tables\ModificationsTrait;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpLiteral;

final class ModificationsDecorator implements ClassDecorator
{

	/** @var string */
	private $modificationsStorage;

	/** @var string */
	private $relatedTableClass;

	/** @var string */
	private $primaryKeyClass;

	public function __construct(string $relatedTable, string $primaryKeyClass)
	{
		$this->modificationsStorage = '$this->modifications';
		$this->relatedTableClass = $relatedTable;
		$this->primaryKeyClass = $primaryKeyClass;
	}


	public function decorate(ClassType $classType, ClassDefinition $definition): void
	{
		$namespace = $classType->getNamespace();
		\assert($namespace !== NULL, 'Class Generator always generate class in namespace.');

		$namespace->addUse(ModificationsTrait::class);
		$classType->addTrait(ModificationsTrait::class);

		$namespace->addUse(Modifications::class);
		$classType->addImplement(Modifications::class);

		// ::update() constructor
		$namespace->addUse($this->primaryKeyClass);
		$classType->addMethod('update')
			->setStatic()
			->setVisibility('public')
			->setReturnType('self')
			->setParameters([
				(new Parameter('primaryKey'))
					->setTypeHint($this->primaryKeyClass)
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
				new PhpLiteral($namespace->unresolveName($this->relatedTableClass))
			]);

		// modify*() methods
		foreach ($definition->getFields() as $fieldName => $type) {
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
						->setTypeHint($type->getTypeHint())
						->setNullable($type->isNullable())
				]);
			$modifier->setReturnType('void');


			// add phpDoc type hints if necessary
			if ($type->requiresDocComment()) {
				$docCommentType = $type->getDocCommentType($namespace);

				$modifier->addComment(\sprintf(
					'@param $%s %s%s',
					$fieldName,
					$docCommentType,
					$type->hasComment() ? ' ' . $type->getComment($namespace) : ''
				));
			}
		}
	}
}
