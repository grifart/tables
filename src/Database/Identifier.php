<?php
declare(strict_types = 1);

namespace Grifart\Tables\Database;

use Dibi\Expression;
use Dibi\Literal;
use function Phun\map;

final class Identifier
{
	/** @var string[] */
	private array $nameParts;

	public function __construct(string $namePart, string ...$nameParts)
	{
		$this->nameParts = [$namePart, ...$nameParts];
	}

	public function toSql(): Expression
	{
		$args = map(
			$this->nameParts,
			static function (string $v): Expression|Literal {
				if (\preg_match('/^[a-z_]*$/', $v) === 1) {
					return new Literal($v);
				}

				return new Expression('%n', $v);
			}
		);

		return new Expression(
			\implode('.', map($args, fn() => '?')),
			...$args,
		);
	}
}
