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
			fn($item, $index) => $item !== null ? $this->types[$index]->fromDatabase($item) : null,
		);
	}

	/**
	 * @return mixed[]
	 */
	private function parseComposite(string $value, int $start = 0, ?int &$end = null): array
	{
		\assert($value[$start] === '(');

		$result = [];

		$inString = false;
		$isString = false;
		$length = \strlen($value);
		$item = '';
		for ($i = $start + 1; $i < $length; $i++) {
			$char = $value[$i];

			if ( ! $inString && $char === ')') {
				$result[] = $isString || $item !== '' ? $item : null;
				$end = $i;
				break;
			}

			if ( ! $inString && $char === '(') {
				// parse to the end but only keep raw value so that it can be recursively parsed in fromDatabase()
				$subCompositeStart = $i;
				$this->parseComposite($value, $i, $i);
				$item = \substr($value, $subCompositeStart, $i - $subCompositeStart);
			} elseif ( ! $inString && $char === ',') {
				$result[] = $isString || $item !== '' ? $item : null;
				$isString = false;
				$item = '';
			} elseif ( ! $inString && $char === '"') {
				$inString = $isString = true;
			} elseif ($inString && $char === "\\" && $value[$i - 1] === "\\") {
				$item = \substr($item, 0, -1) . $char;
			} elseif ($inString && $char === '"' && $value[$i + 1] === '"') {
				$item .= $char;
				$i++;
			} elseif ($inString && $char === '"' && $value[$i + 1] !== '"') {
				$inString = false;
			} else {
				$item .= $char;
			}
		}

		return $result;
	}
}
