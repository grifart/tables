<?php

declare(strict_types=1);

namespace Grifart\Tables\Tests\Fixtures;

use Dibi\Connection;
use Grifart\ClassScaffolder\Definition\Types\Type as PhpType;
use Grifart\Tables\TableManager;
use Grifart\Tables\Type;
use Grifart\Tables\TypeResolver;
use Grifart\Tables\Types\IntType;
use Grifart\Tables\Types\TextType;
use Nette\StaticClass;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

final class TestFixtures
{
	use StaticClass;

	public static function createTableManager(Connection $connection): TableManager
	{
		return new TableManager($connection);
	}

	public static function createTypeResolver(): TypeResolver
	{
		$typeResolver = new TypeResolver();
		$typeResolver->addResolutionByTypeName('uuid', new UuidType());
		$typeResolver->addResolutionByLocation('public.test.score', new class implements Type {
			public function getPhpType(): PhpType
			{
				return resolve('int');
			}

			public function toDatabase(mixed $value): mixed
			{
				return $value;
			}

			public function fromDatabase(mixed $value): mixed
			{
				return $value !== null ? (int) $value : null;
			}
		});
		$typeResolver->addResolutionByTypeName('character varying', new class implements Type {
			public function getPhpType(): PhpType
			{
				return resolve('string');
			}

			public function toDatabase(mixed $value): mixed
			{
				return $value;
			}

			public function fromDatabase(mixed $value): mixed
			{
				return $value;
			}
		});
		return $typeResolver;
	}
}
