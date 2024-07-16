<?php

declare(strict_types=1);

namespace Grifart\Tables;

final class ColumnMetadata
{

	public function __construct(
		private string $name,
		private string $type,
		private bool $nullable,
		private bool $hasDefaultValue,
		private bool $isGenerated,
	) {}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function isNullable(): bool
	{
		return $this->nullable;
	}

	public function hasDefaultValue(): bool
	{
		return $this->hasDefaultValue;
	}

	public function isGenerated(): bool
	{
		return $this->isGenerated;
	}

}
