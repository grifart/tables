<?php

declare(strict_types=1);

namespace Grifart\Tables\Rector;

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
	$rectorConfig->rules([
		DeprecatedModificationsSetterToPropertyRector::class,
		DeprecatedRowGetterToPropertyRector::class,
		DeprecatedTableColumnGetterToPropertyRector::class,
	]);
};
