# grifart/tables

A simple library to access and manipulate database records. Built on top of [Dibi](https://github.com/dg/dibi) and hardwired for PostgreSQL because why would you want to use anything else?

## Installation

```shell
composer require grifart/tables
```

Although optional, we highly recommend you install [grifart/scaffolder](https://github.com/grifart/scaffolder) as well. This will help you generate lots of boilerplate code.

```shell
composer require grifart/scaffolder
```

## Quick start

1. **Register the tables DI extension.** Tables expect that an instance of [Dibi](https://github.com/dg/dibi) is also configured and registered in the container.

    ```neon
    extensions:
        tables: Grifart\Tables\DI\TablesExtension
    ```

2. **Create a database table.** You can use your favourite database migration tool.

    ```postgresql
    CREATE TABLE "article" (
      "id" uuid NOT NULL PRIMARY KEY,
      "title" varchar NOT NULL,
      "text" text NOT NULL,
      "createdAt" timestamp without time zone NOT NULL,
      "deletedAt" timestamp without time zone DEFAULT NULL,
      "published" boolean NOT NULL
    );
    ```

3. **Create a definition file for scaffolder.** Tables expose a helper that creates all necessary class definitions for you:

    ```php
    <?php
  
    use Grifart\Tables\Scaffolding\PostgresReflector;
    use Grifart\Tables\Scaffolding\Scaffolding;
    use Grifart\Tables\TypeResolver;
  
    // create a DI container
    $container = App\Bootstrap::boot();
  
    return Scaffolding::definitionsForPgTable(
        $container->getByType(PostgresReflector::class),
        $container->getByType(TypeResolver::class),
        'public', // table schema
        'article', // table name
        ArticleRow::class,
        ArticleChangeSet::class,
        ArticlesTable::class,
        ArticlePrimaryKey::class,
    );
    ```

    Once you [run scaffolder](https://github.com/grifart/scaffolder), it will inspect the database schema and generate a set of four classes:

    - `ArticlesTable`, a service that provides API for accessing and manipulating data in the `article` table;
    - `ArticleRow`, a simple DTO that wraps a single row from the `article` table;
    - `ArticleChangeSet`, a mutable wrapper over data to be persisted in the `article` table,
    - `ArticlePrimaryKey`, a representation of the `article` table's primary key.

4. **Register the `ArticlesTable` in your DI container.**

  ```neon
  services:
      - ArticlesTable
  ```

## Usage

Use dependency injection to retrieve an instance of the `ArticlesTable` service in your model layer. The table class exposes the following methods:

### Read

You can list all records in the table by calling the `getAll()` method. The method optionally accepts sorting criteria (more on that below);

```php
$rows = $table->getAll($orderBy);
```

To fetch a specific record from the table, use either the `find()` or `get()` method with the desired record's primary key. The difference is that `find()` returns `null` if the query yields empty result, whereas `get()` throws an exception in such case:

```php
$row = $table->find(ArticlePrimaryKey::of($articleId));
// or
$row = $table->get(ArticlePrimaryKey::of($articleId));
```

To retrieve a list of records that match given criteria, you can use the `findBy()` method and pass a set of conditions to it (more on that below):

```php
$rows = $table->findBy($conditions, $orderBy);
```

There is also a helper method to retrieve a *single* record that matches given criteria. It throws an exception when the query doesn't yield exactly one result:

```php
$row = $table->getBy($conditions);
```

#### Conditions

When it comes to search criteria, the table expects a `Condition` (or a list thereof). This is how a simple search for published articles might look like:

```php
$rows = $table->findBy(
    Composite::and(
        $table->published()->is(equalTo(true)),
        $table->createdAt()->is(lesserThanOrEqualTo(Instant::now())),
    ),
);
```

This package provides a `Composite` condition that lets you compose the most complex trees of boolean logic together, and a set of most common conditions such as equality, comparison, and null-checks.

In addition to these, you can also write your own condition by implementing the `Condition` interface. It defines the sole method `format()` which is expected to return an array compatible with [Dibi](https://github.com/dg/dibi).

Take a look at how a `LIKE` condition could be implemented. It maps to a `LIKE` database operation with two operands, a sub-expression (more on that below), and a pattern mapped to a database text:

```php
use Grifart\Tables\Expression;
use Grifart\Tables\Types\TextType;
use function Grifart\Tables\Types\mapToDatabase;

final class IsLike implements Condition
{
	/**
	 * @param Expression<string> $expression
	 */
	public function __construct(
		private Expression $expression,
		private string $pattern,
	) {}

	public function format(): array
	{
		return [
			'? LIKE ?',
			$this->expression->toSql(),
			mapToDatabase($this->pattern, new TextType()),
		];
	}
}
```

You can then use the condition like this:

```php
$rows = $table->findBy([
    new IsLike($table->title(), 'Top 10%'),
]);
```

Or create a factory function:

```php
function like(string $pattern) {
    return static fn(Expression $expression) => new IsLike($expression, $pattern);
}
```

And then use it like this:

```php
$rows = $table->findBy([
    $table->title()->is(like('Top 10%')),
]);
```

#### Expressions

Expressions are an abstraction over database expressions. All table columns are expressions and as you've seen, the generated `ArticlesTable` exposes each of them via an aptly named method.

You can also create custom expressions that map to various database functions and operations. You just need to implement the `Expression` interface which requires you to specify the SQL representation of the expression, and also its type (used for formatting values in conditions):

```php
/**
 * @implements Expression<int>
 */
final class Year implements Expression
{
    /**
     * @param Expression<\Brick\DateTime\Instant>|Expression<\Brick\DateTime\LocalDate> $sub
    */
    public function __construct(
        private Expression $sub,
    ) {}

    public function toSql(): \Dibi\Expression
    {
        return new DibiExpression(
            "EXTRACT ('year' FROM ?)",
            $this->sub->toSql(),
        );
    }

    public function getType(): Type
    {
        return new IntType();
    }
}
```

Alternatively, you can extend the `ExpressionWithShorthands` base class:

```php
/**
 * @extends ExpressionWithShorthands<int>
 */
final class Year extends ExpressionWithShorthands
{
    // ...
}
```

That way, the convenient `is()` shorthand will be available on the expression instance:

```php
$rows = $table->findBy(
    (new Year($table->createdAt()))->is(equalTo(2021)),
);
```

You can also use the `expr()` function to create such expression:

```php
$year = fn(Expression $expr) => expr("EXTRACT ('year' FROM ?)", $expr->toSql());
$rows = $table->findBy(
    $year($table->createdAt())->is(equalTo(2021)),
);
```

#### Ordering

To specify the desired order of records, you can provide a list of sorting criteria. This uses the same expression mechanism as filtering. You can use the `Expression`'s shorthand methods `ascending()` and `descending()`:

```php
$rows = $table->getAll(orderBy: [
    $table->createdAt()->descending(),
    $table->title(), // ->ascending() is the default
]);
```

### Insert

To insert a new record into the database table, use the `$table->new()` method. You have to provide all required values (for columns without a default value) to the method:

```php
$changeSet = $table->new(
    id: \Ramsey\Uuid\Uuid::uuid4(),
    title: 'Title of the post',
    text: 'Postt text',
    createdAt: \Brick\DateTime\Instant::now(),
    published: true,
);
```

The method returns a change set which you can further modify, and eventually save:

```php
$changeSet->modifyText('Post text');
$table->save($changeSet);
```

### Update

To update a record in the table, first you need to get an instance of change set for the specific record. You can get one for any given primary key or row:

```php
$changeSet = $table->edit(ArticlePrimaryKey::from($articleId));
// or
$changeSet = $table->edit($articleRow);
```

Then you can add modifications to the change set and finally save it:

```php
$changeSet->modifyDeletedAt(\Brick\DateTime\Instant::now());
$table->save($changeSet);
```

### Delete

To delete a record, you simply need its primary key or row:

```php
$table->delete(ArticlePrimaryKey::from($articleId));
// or
$table->delete($articleRow);
```


## Type mapping

### Basic types

As you might have noticed, Tables provide default mapping for all PostgreSQL's basic types:

- Textual types (`character`, `character varying`, `text`) all map to `string`.
- Integer types (`smallint`, `int`, `bigint`) all map to `int`.
- Boolean type maps to `bool`.
- Binary type (`bytea`) maps to a binary `string`.
- Numeric type (`numeric`/`decimal`) maps to a `BigDecimal` from [brick/math](https://github.com/brick/math) if installed.
- Date-time types (`date`, `time`, `timestamp`) map to `LocalDate`, `LocalType`, and `Instant`, respectively, from [brick/date-time](https://github.com/brick/date-time) if installed.
- Uuid type maps to a `Uuid` from [ramsey/uuid](https://github.com/ramsey/uuid) if installed.
- Json types (`json`, `jsonb`) map to a JSON-decoded PHP value.

### Advanced types

In addition to mapping PostgreSQL's basic types by default, Tables let you make the most of the database's complex type system. You can describe and provide mapping for even the wildest combinations of PostgreSQL types.

#### Type resolver

At the core of the type system in Tables is the `TypeResolver`. It decides which type to use for each column based on its database type, or even its scoped name.

You can register your own types in the config file:

```neon
tables:
    types:
        byName:
            typeName: App\Tables\MyType
        byLocation:
            schema.table.column: App\Tables\MyType
```

Alternatively, you can implement the `TypeResolverConfigurator` interface and register the implementation in the DI container. Tables will automatically pick it up and pass the `TypeResolver` to the configurator's `configure()` method.

#### Custom types

All types implement the `Type` interface and its four methods:

- `getPhpType(): PhpType` returns the scaffolder-compatible type of the represented PHP value;
- `getDatabaseTypes(): string[]` returns a list of database type names – these are used when the type is registered using the `TypeResolver::addType($type)` method;
- `toDatabase(mixed $value): mixed` maps a PHP value of given type to its database representation;
- `fromDatabase(mixed $value): mixed` maps a database representation to its respective PHP value.

There are also a few helpers for creating the most common advanced types:

#### Array types

You can map values to an array via the `ArrayType`. This formats the items using the declared subtype, and serializes them into a PostgreSQL array. Example of an array of dates:

```php
$dateArrayType = ArrayType::of(new DateType());
```

#### Enum types

You can map native PHP enumerations to PostgreSQL's enums using the `EnumType`. This requires that the provided enum is a `\BackedEnum`, and serializes it to its backing value:

```php
enum Status: string {
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}

$statusType = EnumType::of(Status::class);
```

#### Composite types

There is also a base class for describing composite types:

```php
$moneyType = new class extends CompositeType {
    public function __construct()
    {
        parent::__construct(new DecimalType(), new CurrencyType());
    }

    public function getPhpType(): PhpType
    {
        return resolve(Money::class);
    }

    public function getDatabaseTypes(): array
    {
        return [];
    }

    public function toDatabase(mixed $value): mixed
    {
        return $this->tupleToDatabase([
            $value->getAmount(),
            $value->getCurrency(),
        ]);
    }

    public function fromDatabase(mixed $value): mixed
    {
        [$amount, $currency] = $this->tupleFromDatabase($value);
        return Money::of($amount, $currency);
    }
};
```
