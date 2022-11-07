<?php
declare(strict_types = 1);

namespace Grifart\Tables;


final class NamedIdentifier
{
	/** @var string[] */
	private array $nameParts = [];

	public function __construct(string ...$nameParts) {
		$this->nameParts = $nameParts;
	}

	/**
	 * @return string SQL
	 */
	public function toSql(): string {

		// todo: use pg_escape_identifier() or Dibi-equivalent
		// todo: should not add quotes when not necessary!
		$escapeIdentifier = static function($v) {
			\assert(! str_contains($v, '"'), 'Whoops, this temporary escaping could not escape strings with a " character.');
			return sprintf('"%s"', $v);
		};

		return implode(".", array_map($escapeIdentifier, $this->nameParts));
	}

}
