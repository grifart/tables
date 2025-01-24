<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;


use Dibi\Connection;
use Grifart\Tables\ColumnMetadata;
use Grifart\Tables\MissingPrimaryIndex;
use function Phun\map;
use function Phun\reindex;

final class PostgresReflector
{

	public function __construct(
		private Connection $connection
	) {}

	/**
	 * source: https://stackoverflow.com/a/51897900/631369
	 *
	 * @return ColumnMetadata[]
	 */
	function retrieveColumnMetadata(string $schema, string $table): array {
		$result = $this->connection->query(<<<SQL
	SELECT
		pg_attribute.attname as name,
		pg_catalog.format_type(pg_attribute.atttypid, NULL) as type,
		not(pg_attribute.attnotnull) AS nullable,
		pg_attribute.atthasdef OR pg_attribute.attidentity != '' AS `hasDefaultValue`,
		pg_attribute.attgenerated != '' OR pg_attribute.attidentity = 'a' AS `isGenerated`
	FROM pg_catalog.pg_attribute pg_attribute
	WHERE pg_attribute.attnum > 0
		AND NOT pg_attribute.attisdropped
		AND pg_attribute.attrelid = (
			SELECT pg_class.oid
			FROM pg_catalog.pg_class pg_class
			LEFT JOIN pg_catalog.pg_namespace pg_namespace ON pg_namespace.oid = pg_class.relnamespace
			WHERE pg_namespace.nspname = %s AND pg_class.relname = %s
		);
SQL
			,$schema, $table);
		$results = [];
		foreach($result->fetchAssoc('name') as $columnName => $columnInfo) {
			\assert($columnInfo instanceof \Dibi\Row);
			$results[$columnName] = new ColumnMetadata(
				$columnInfo['name'],
				$columnInfo['type'],
				$columnInfo['nullable'],
				$columnInfo['hasDefaultValue'] xor $columnInfo['nullable'], // it has explicit default value or it has not, but it is nullable so `null` is its implicit default value – see https://gitlab.grifart.cz/grifart/tables/-/issues/9
				$columnInfo['isGenerated'],
			);
		}
		return $results;
	}

	/**
	 * @return string[]
	 */
	public function retrievePrimaryKeyColumns(string $schema, string $table): array
	{
		$columnsByPosition = $this->connection->query(<<<SQL
SELECT pg_attribute.attnum, pg_attribute.attname
FROM pg_catalog.pg_attribute
JOIN pg_catalog.pg_class ON pg_class.oid = pg_attribute.attrelid
LEFT JOIN pg_catalog.pg_namespace ON pg_namespace.oid = pg_class.relnamespace
WHERE pg_namespace.nspname = %s AND pg_class.relname = %s;
SQL
			, $schema, $table)->fetchPairs('attnum', 'attname');

		$rawPrimaryKeyColumnPositions = $this->connection->query(<<<SQL
SELECT pg_index.indkey
FROM pg_catalog.pg_index
JOIN pg_catalog.pg_class ON pg_class.oid = pg_index.indrelid
LEFT JOIN pg_catalog.pg_namespace ON pg_namespace.oid = pg_class.relnamespace
WHERE pg_index.indisprimary AND pg_namespace.nspname = %s AND pg_class.relname = %s;
SQL
			, $schema, $table)->setType('indkey', null)->fetchSingle();

		if ($rawPrimaryKeyColumnPositions === null) {
			throw MissingPrimaryIndex::in($schema, $table);
		}

		$primaryKeyColumnPositions = \explode(' ', $rawPrimaryKeyColumnPositions);
		return reindex(
			map(
				$primaryKeyColumnPositions,
				static fn(string $position) => $columnsByPosition[(int) $position],
			),
			static fn(string $name) => $name,
		);
	}

}
