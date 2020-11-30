<?php declare(strict_types=1);


namespace Grifart\Tables\Scaffolding;


use Dibi\Connection;

final class PostgresReflector
{

	public function __construct(
		private Connection $connection
	) {}

	/**
	 * source: https://stackoverflow.com/a/51897900/631369
	 * @return Column[]
	 */
	function retrieveColumnInfo(string $schema, string $table): array {
		$result = $this->connection->query(<<<SQL
	SELECT
	`pg_attribute`.attname                                                    as `name`,
	pg_catalog.format_type(`pg_attribute`.atttypid, `pg_attribute`.atttypmod) as `type`,
	not(`pg_attribute`.attnotnull) AS `nullable`,
	`pg_attribute`.atthasdef AS `hasDefaultValue`
FROM
	pg_catalog.pg_attribute `pg_attribute`
WHERE
	`pg_attribute`.attnum > 0
	AND NOT `pg_attribute`.attisdropped
	AND `pg_attribute`.attrelid = (
	SELECT `pg_class`.oid
		FROM pg_catalog.pg_class `pg_class`
			LEFT JOIN pg_catalog.pg_namespace `pg_namespace` ON `pg_namespace`.oid = `pg_class`.relnamespace
		WHERE
			`pg_namespace`.nspname = %s
			AND `pg_class`.relname = %s
	);
SQL
			,$schema, $table);
		$results = [];
		foreach($result->fetchAssoc('name') as $columnName => $columnInfo) {
			\assert($columnInfo instanceof \Dibi\Row);
			$results[$columnName] = new Column($columnInfo['name'], $columnInfo['type'], $columnInfo['nullable'], $columnInfo['hasDefaultValue']);
		}
		return $results;
	}

}
