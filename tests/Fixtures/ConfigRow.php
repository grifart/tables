<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Grifart\Tables\Row;

final class ConfigRow implements Row
{
	private function __construct(
		private Uuid $id,
		private string $key,
		private string $value,
	) {
	}


	public function getId(): Uuid
	{
		return $this->id;
	}


	public function getKey(): string
	{
		return $this->key;
	}


	public function getValue(): string
	{
		return $this->value;
	}


	public static function reconstitute(array $values): static
	{
		/** @var array{id: Uuid, key: string, value: string} $values */
		return new static($values['id'], $values['key'], $values['value']);
	}
}
