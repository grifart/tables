# Migrate to composite field

Sometimes you start with table containing values, which you then see have dependency on each other. As happened with [Stamp object](https://gitlab.grifart.cz/ivy/server/-/merge_requests/222/diffs#note_69354) (originally named `auditTrail`).

### 1. migrating database

```sql
-- prepare new data type in PostgreSQL
CREATE TYPE "public"."auditTrail" AS (
	"occurredAt" timestamp without time zone,
	"causedBy" uuid
);

-- Add column with default value
ALTER TABLE "clinic"."patient"
	ADD COLUMN "created" "public"."auditTrail" DEFAULT NULL;

-- migrate data from two original columns to the new composite one
UPDATE "clinic"."patient" SET
	"created" = ROW("createdAt", "createdBy");

-- removed old one and remove DEFAULT value (as it should be required filed at the end)
ALTER TABLE "clinic"."patient"
	ALTER COLUMN "created" SET NOT NULL,
	ALTER COLUMN "created" DROP DEFAULT,
	DROP COLUMN "createdAt",
	DROP COLUMN "createdBy";
```

### 2. Mapper

And add mapping of new field. It is useful to use PostgreSQL tools for composite & array types. This helper class will provide you logic for (de)serializing these composite types into standard SQL query.

```php
<?php
		$phpToDatabaseMapper->addMapping(
			static fn(string $dbType): ?string => $dbType === 'stamp' ? Stamp::class : null,
			static fn(Stamp $trail): string => PostgreSQLTools::toPgComposite([
				(string) $trail->getOccurredAt(),
				$trail->getCausedBy()->toString(),
			]),
		);
		$databaseToPhpMapper->addMapping(
			static fn(string $dbType): ?string => $dbType === 'stamp' ? Stamp::class : null,
			static function (string $value): Stamp {
				[$occurredAt, $causedBy] = PostgreSQLTools::fromPgComposite($value);
				$occurredAtLocal = LocalDateTime::parse($occurredAt, ISO8601Parsers::dateTime());
				return Stamp::from(
					AccountId::of($causedBy),
					$occurredAtLocal->atTimeZone(TimeZone::utc())->getInstant(),
				);
			},
		);
```
