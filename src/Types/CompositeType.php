<?php

declare(strict_types=1);

namespace Grifart\Tables\Types;

use Dibi\Expression;
use Dibi\Literal;
use Grifart\Tables\Database\DatabaseType;
use Grifart\Tables\Type;
use Grifart\Tables\UnexpectedNullValue;
use function Phun\map;
use function Phun\mapWithKeys;

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
	protected function __construct(
		private DatabaseType $databaseType,
		Type $type,
		Type ...$rest,
	)
	{
		$this->types = [$type, ...$rest];
	}

	final public function getDatabaseType(): DatabaseType
	{
		return $this->databaseType;
	}

	/**
	 * @param mixed[] $value
	 */
	protected function tupleToDatabase(array $value): Expression
	{
		$args = [
			new Literal('ROW('),
			...mapWithKeys(
				$value,
				function (int $index, mixed $item) {
					$itemType = $this->types[$index];

					if ($item === null && ! $itemType instanceof NullableType) {
						throw new UnexpectedNullValue();
					}

					return $itemType->toDatabase($item);
				},
			),
			new Literal(')::'),
			$this->getDatabaseType()->toSql(),
		];

		return new Expression(
			'?' . \implode(',', map($value, fn() => '?')) . '??',
			...$args,
		);
	}

	/**
	 * @return mixed[]
	 */
	protected function tupleFromDatabase(string $value): array
	{
		$result = $this->parseComposite($value);

		\assert(\count($result) === \count($this->types));
		return mapWithKeys(
			$result,
			function (int $index, mixed $item) {
				$itemType = $this->types[$index];

				if ($item === null && ! $itemType instanceof NullableType) {
					throw new UnexpectedNullValue();
				}

				return $itemType->fromDatabase($item);
			},
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
