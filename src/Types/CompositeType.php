<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Grifart\Tables\Type;
use function Functional\map;

/**
 * @template T
 * @implements Type<T>
 */
abstract class CompositeType implements Type
{
	/** @var Type<mixed>[] */
	private array $types;

	/**
	 * @param Type<mixed> $type
	 * @param Type<mixed> ...$rest
	 */
	protected function __construct(Type $type, Type ...$rest)
	{
		$this->types = [$type, ...$rest];
	}

	/**
	 * @param mixed[] $value
	 */
	protected function tupleToDatabase(array $value): string
	{
		\assert(\count($value) === \count($this->types));
		return \sprintf(
			'(%s)',
			\implode(
				',',
				map(
					$value,
					function ($item, $index) {
						if ($item === null) {
							return '';
						}

						$mappedItem = $this->types[$index]->toDatabase($item);

						if ($mappedItem === '') {
							return '""';
						}

						if (\preg_match('/[,\s"()]/', (string) $mappedItem)) {
							return \sprintf(
								'"%s"',
								\str_replace(['\\', '"'], ['\\\\', '\\"'], (string) $mappedItem),
							);
						}

						return $mappedItem;
					},
				),
			)
		);
	}

	/**
	 * @return mixed[]
	 */
	protected function tupleFromDatabase(string $value): ?array
	{
		if ($value === '') {
			return null;
		}

		$result = $this->parseComposite($value);

		\assert(\count($result) === \count($this->types));
		return map(
			$result,
			fn($item, $index) => $this->types[$index]->fromDatabase($item),
		);
	}

	/**
	 * @return mixed[]
	 */
	private function parseComposite(string $value, int $start = 0, ?int &$end = null): array
	{
		\assert($value[$start] === '(');

		$result = [];

		$string = false;
		$length = \strlen($value);
		$item = '';
		for ($i = $start + 1; $i < $length; $i++) {
			$char = $value[$i];

			if ( ! $string && $char === ')') {
				if ($item !== '') {
					$result[] = $item;
				}
				$end = $i;
				break;
			}

			if ( ! $string && $char === '(') {
				// parse to the end but only keep raw value so that it can be recursively parsed in fromDatabase()
				$subCompositeStart = $i;
				$this->parseComposite($value, $i, $i);
				$item = \substr($value, $subCompositeStart, $i - $subCompositeStart);
			} elseif ( ! $string && $char ===',') {
				$result[] = $item !== '' ? $item : null;
				$item = '';
			} elseif ( ! $string && $char === '"') {
				$string = true;
			} elseif ($string && $char === "\\" && $value[$i - 1] === "\\") {
				$item = \substr($item, 0, -1) . $char;
			} elseif ($string && $char === '"' && $value[$i + 1] === '"') {
				$item .= $char;
				$i++;
			} elseif ($string && $char === '"' && $value[$i + 1] !== '"') {
				$string = false;
			} else {
				$item .= $char;
			}
		}

		return $result;
	}
}
