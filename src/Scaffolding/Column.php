<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;


final class Column
{

	/** @var string */
	private $name;

	/** @var string */
	private $type;

	/** @var bool */
	private $nullable;

	public function __construct(string $name, string $type, bool $nullable)
	{
		$this->name = $name;
		$this->type = $type;
		$this->nullable = $nullable;
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



}
