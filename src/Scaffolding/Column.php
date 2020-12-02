<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;


final class Column
{

	public function __construct(
		private string $name,
		private string $type,
		private bool $nullable,
		private bool $hasDefaultValue,
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

}
