<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;


final class Column
{

	private string $name;

	private string $type;

	private bool $nullable;

	private bool $hasDefaultValue;

	public function __construct(string $name, string $type, bool $nullable, bool $hasDefaultValue)
	{
		$this->name = $name;
		$this->type = $type;
		$this->nullable = $nullable;
		$this->hasDefaultValue = $hasDefaultValue;
	}

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
