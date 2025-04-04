<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Dibi\Expression;
use Dibi\Literal;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Database\ArrayType as DatabaseArrayType;
use Grifart\Tables\Type;
use Grifart\Tables\UnexpectedNullValue;
use function Grifart\ClassScaffolder\Definition\Types\listOf;
use function Phun\map;

/**
 * @template ItemType
 * @implements Type<ItemType[]>
 */
final class ArrayType implements Type
{
	/**
	 * @param Type<ItemType> $itemType
	 */
	private function __construct(
		private Type $itemType,
	) {}

	/**
	 * @template FromItemType
	 * @param Type<FromItemType> $itemType
	 * @return ArrayType<FromItemType>
	 */
	public static function of(Type $itemType): self
	{
		return new self($itemType);
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

	public function getDatabaseType(): DatabaseArrayType
	{
		return new DatabaseArrayType($this->itemType->getDatabaseType());
	}

	public function toDatabase(mixed $value): Expression
	{
		$args = [
			new Literal('ARRAY['),
			...map(
				$value,
				function (mixed $item) {
					if ($item === null && ! $this->itemType instanceof NullableType) {
						throw new UnexpectedNullValue();
					}

					return $this->itemType->toDatabase($item);
				},
			),
			new Literal(']::'),
			$this->getDatabaseType()->toSql(),
		];

		return new Expression(
			'?' . \implode(',', map($value, fn() => '?')) . '??',
			...$args,
		);
	}

	public function fromDatabase(mixed $value): array
	{
		$result = $this->parseArray($value);
		return map(
			$result,
			function ($item) {
				if ($item === null && ! $this->itemType instanceof NullableType) {
					throw new UnexpectedNullValue();
				}

				return $this->itemType->fromDatabase($item);
			},
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
					$result[] = $item !== 'NULL' ? $item : null;
				}
				$end = $i;
				break;
			}

			if ( ! $string && $char === '{') {
				// parse to the end but only keep raw value so that it can be recursively parsed in fromDatabase()
				$subArrayStart = $i;
				$this->parseArray($value, $i, $i);
				$item = \substr($value, $subArrayStart, $i - $subArrayStart + 1);
			} elseif ( ! $string && $char === ',') {
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
