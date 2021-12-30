<?php

declare(strict_types=1);

namespace Grifart\Tables\Conditions;

use function Functional\map;

final class Composite implements Condition
{
	/**
	 * @param string $operator
	 * @param Condition[] $conditions
	 */
	private function __construct(
		private string $operator,
		private array $conditions,
	) {}

	/**
	 * @param Condition ...$conditions
	 */
	public static function and(Condition ...$conditions): self
	{
		return new self('%and', $conditions);
	}

	/**
	 * @param Condition ...$conditions
	 */
	public static function or(Condition ...$conditions): self
	{
		return new self('%or', $conditions);
	}

	public function format(): array
	{
		return [
			$this->operator,
			map(
				$this->conditions,
				static fn(Condition $condition) => $condition->format(),
			),
		];
	}
}
