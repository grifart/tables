<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Dibi\Literal;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\NamedIdentifier;
use Grifart\Tables\Type;
use PhpParser\Node\Name;
use function Functional\map;
use function Grifart\ClassScaffolder\Definition\Types\listOf;

/**
 * @template ItemType
 * @implements Type<ItemType[]>
 */
final class ArrayType implements Type // @todo: There is implicit support for nullable types, shouldn't it be explicit instead?
{

	private string $databaseType;

	/**
	 * @param Type<ItemType> $itemType
	 */
	private function __construct(
		NamedIdentifier|string $databaseType,
		private Type $itemType, // @todo: Should we have just `getDatabaseType()` and wrap this type automatically?
	) {
		$this->databaseType = $databaseType instanceof NamedIdentifier
			? $databaseType->toSql() . '[]' // @todo: what about multi-dimensional arrays? int[][]?
			: $databaseType;
	}

	/**
	 * @template FromItemType
	 * @param Type<FromItemType> $itemType
	 * @return ArrayType<FromItemType>
	 */
	public static function of(NamedIdentifier|string $databaseType, Type $itemType): self
	{
		return new self($databaseType, $itemType);
	}

	/**
	 * @return Type<ItemType>
	 */
	public function getItemType(): Type
	{
		return $this->itemType;
	}

	public function getPhpType(): PhpType
	{
		return listOf($this->itemType->getPhpType());
	}

	public function getDatabaseTypes(): array
	{
		return [$this->databaseType];
	}

	public function toDatabase(mixed $value): Literal
	{
		return new Literal(
			\sprintf(
				'ARRAY[%s]::%s',
				\implode(',', map($value, function (mixed $item) {
					if ($item === null) {
						return 'null';
					}

					/** @var ItemType $item */
					$mapped = $this->itemType->toDatabase($item);
					// @todo: we need connection here to call escape functiom ?!
					if (is_string($mapped)) {
						// @todo remove me! This is re-implementation of escaping
						// @todo Should be escaped by TestType at a first place, hmm?
						// @todo Shouldn't there be Literal as a required return type of toDatabse?
						return "'" . str_replace(["\\","'"], ["\\\\","''"], $mapped) . "'";
					}
					return $mapped;
				})),
				$this->databaseType
			)
		);
	}

	public function fromDatabase(mixed $value): array
	{
		$result = $this->parseArray($value);
		return map(
			$result,
			fn($item) => $item !== null ? $this->itemType->fromDatabase($item) : null,
		);
	}

	/**
	 * @return mixed[]
	 */
	private function parseArray(string $value, int $start = 0, ?int &$end = null): array
	{
		\assert($value[$start] === '{');

		$result = [];

		$string = false;
		$length = \strlen($value);
		$item = '';
		for ($i = $start + 1; $i < $length; $i++) {
			$char = $value[$i];

			if ( ! $string && $char === '}') {
				if ($item !== '') {
					$result[] = $item;
				}
				$end = $i;
				break;
			}

			if ( ! $string && $char === '{') {
				// parse to the end but only keep raw value so that it can be recursively parsed in fromDatabase()
				$subArrayStart = $i;
				$this->parseArray($value, $i, $i);
				$item = \substr($value, $subArrayStart, $i - $subArrayStart + 1);
			} elseif ( ! $string && $char ===',') {
				$result[] = $item !== 'NULL' ? $item : null;
				$item = '';
			} elseif ( ! $string && $char === '"') {
				$string = true;
			} elseif ($string && ($char === '"' || $char === "\\") && $value[$i - 1] === "\\") {
				$item = \substr($item, 0, -1) . $char;
			} elseif ($string && $char === '"' && $value[$i - 1] !== "\\") {
				$string = false;
			} else {
				$item .= $char;
			}
		}

		return $result;
	}
}
