<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final readonly class ConfigRow implements Row
{
	private function __construct(
		public Uuid $id,
		public string $key,
		public string $value,
	) {
	}


	#[\Deprecated('Use $id property instead.')]
	public function getId(): Uuid
	{
		return $this->id;
	}


	#[\Deprecated('Use $key property instead.')]
	public function getKey(): string
	{
		return $this->key;
	}


	#[\Deprecated('Use $value property instead.')]
	public function getValue(): string
	{
		return $this->value;
	}


	#[\Override]
	public static function reconstitute(array $values): static
	{
		/** @var array{id: Uuid, key: string, value: string} $values */
		return new static($values['id'], $values['key'], $values['value']);
	}
}
