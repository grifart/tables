<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\Type;
use function Functional\map;
use function Grifart\ClassScaffolder\Definition\Types\listOf;

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

	public function getPhpType(): PhpType
	{
		return listOf($this->itemType->getPhpType());
	}

	public function getDatabaseTypes(): array
	{
		return [];
	}

	public function toDatabase(mixed $value): string
	{
		return \sprintf(
			'{%s}',
			\implode(',', map($value, function (mixed $item) {
				if ($item === null) {
					return 'NULL';
				}

				/** @var ItemType $item */
				$mapped = (string) $this->itemType->toDatabase($item);
				if ($mapped === '') {
					return '""';
				}

				if (\preg_match('/[,\\"\s]/', $mapped) && ! ($this->itemType instanceof self)) {
					return \sprintf('"%s"', \addcslashes($mapped, '"\\'));
				}

				return $mapped;
			})),
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
