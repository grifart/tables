<?php

declare(strict_types=1);

namespace Grifart\Tables;

use Nette\StaticClass;
use Nette\Utils\Strings;

final class CaseConversion
{
	use StaticClass;

	public static function toUnderscores(string $input): string {
		$withUnderscores = \preg_replace(
			\sprintf('/%s|%s|%s/',
				'(?<=[A-Z])(?=[A-Z][a-z])',
				'(?<=[^A-Z])(?=[A-Z])',
				'(?<=[A-Za-z])(?=[^A-Za-z])'
			),
			'_',
			$input
		);

		\assert(\is_string($withUnderscores));
		return Strings::upper($withUnderscores);
	}

}
