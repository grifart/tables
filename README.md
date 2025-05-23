# grifart/tables

A simple library to access and manipulate database records. Built on top of [Dibi](https://github.com/dg/dibi) and **hardwired for PostgreSQL.**

This library is developed at [gitlab.grifart.cz/grifart/tables](https://gitlab.grifart.cz/grifart/tables) and distributed using [github.com/grifart/tables](https://github.com/grifart/tables). GitLab repository is automatically mirrored to GitHub for all protected branches and tags. Development branches can be found only at GitLab.

## Installation

```shell
composer require grifart/tables
```

## Quick start

1. **Register the tables DI extension.** Tables expect that an instance of [Dibi](https://github.com/dg/dibi) is also configured and registered in the container.

    ```neon
    extensions:
        tables: Grifart\Tables\DI\TablesExtension
    ```

2. **Create a database table.** You can use your favourite database migration tool.

    ```sql
    CREATE TABLE "article" (
      "id" uuid NOT NULL PRIMARY KEY,
      "title" varchar NOT NULL,
      "text" text NOT NULL,
      "createdAt" timestamp without time zone NOT NULL,
      "deletedAt" timestamp without time zone DEFAULT NULL,
      "published" boolean NOT NULL
    );
    ```

3. **Create a definition file for [scaffolder](https://github.com/grifart/scaffolder).** Tables expose a helper that creates all necessary class definitions for you:

    ```php
    <?php

    use Grifart\Tables\Scaffolding\TablesDefinitions;

    // create a DI container, the same way as you do in your application's bootstrap.php, e.g.
    $container = App\Bootstrap::boot();

    // grab the definitions factory from the container
    $tablesDefinitions = $container->getByType(TablesDefinitions::class);

    return $tablesDefinitions->for(
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

You can list all records in the table by calling the `getAll()` method. The method optionally accepts sorting criteria and a paginator (more on both below).

```php
$rows = $table->getAll($orderBy, $paginator);
```

To fetch a specific record from the table, use either the `find()` or `get()` method with the desired record's primary key. The difference is that `find()` returns `null` if the query yields empty result, whereas `get()` throws an exception in such case:

```php
$row = $table->find(ArticlePrimaryKey::of($articleId));
// or
$row = $table->get(ArticlePrimaryKey::of($articleId));
```

To retrieve a list of records that match given criteria, you can use the `findBy()` method and pass a set of conditions to it (more on that below):

```php
$rows = $table->findBy($conditions, $orderBy, $paginator);
```

There are also two pairs of helper methods to retrieve a *single* record that matches given criteria: `getUniqueBy()` and `findUniqueBy()` look for a unique record and throw an exception when the query yields more than one result. In addition, `getUniqueBy()` fails if no record is found, whereas `findUniqueBy()` returns null in such case.

```php
$row = $table->getUniqueBy($conditions);
$rowOrNull = $table->findUniqueBy($conditions);
```

And `getFirstBy()` and `findFirstBy()` return the first record that matches given criteria, regardless of whether there are more of them in the table.

```php
$row = $table->getFirstBy($conditions, $orderBy);
$rowOrNull = $table->findFirstBy($conditions, $orderBy);
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

The code above could be simplified to a list of conditions – if a list is passed, the `and` relationship is assumed implicitly:

```php
$rows = $table->findBy([
    $table->published()->is(equalTo(true)),
    $table->createdAt()->is(lesserThanOrEqualTo(Instant::now())),
]);
```

Also, the `is()` method defaults to equality check, so you can omit the `equalTo()` and pass the value directly:

```php
$rows = $table->findBy([
    $table->published()->is(true),
    $table->createdAt()->is(lesserThanOrEqualTo(Instant::now())),
]);
```

This package provides a `Composite` condition that lets you compose the most complex trees of boolean logic together, and a set of most common conditions such as equality, comparison, and null-checks. For a complete list, look into the [`Conditions/functions.php`](../src/Conditions/functions.php) file.

In addition to these, you can also write your own conditions by implementing the `Condition` interface. It defines the sole method `toSql()` which is expected to return an array compatible with [Dibi](https://github.com/dg/dibi).

Take a look at how a `LIKE` condition could be implemented. It maps to a `LIKE` database operation with two operands, a sub-expression (more on that below), and a pattern mapped to a database text:

```php
use Grifart\Tables\Expression;
use Grifart\Tables\Types\TextType;

final class IsLike implements Condition
{
	/**
	 * @param Expression<string> $expression
	 */
	public function __construct(
		private Expression $expression,
		private string $pattern,
	) {}

	public function toSql(): \Dibi\Expression
	{
		return new \Dibi\Expression(
			'? LIKE ?',
			$this->expression->toSql(),
			TextType::varchar()->toDatabase($this->pattern),
		);
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
use Grifart\Tables\Expression;
use Grifart\Tables\Types\IntType;
use Grifart\Tables\Type;

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
        return new \Dibi\Expression(
            "EXTRACT ('year' FROM ?)",
            $this->sub->toSql(),
        );
    }

    public function getType(): Type
    {
        return IntType::integer();
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
$year = fn(Expression $expr) => expr(IntType::integer(), "EXTRACT ('year' FROM ?)", $expr->toSql());
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

#### Pagination

The `getAll` and `findBy` methods also optionally accept an instance of `Nette\Utils\Paginator`. If you provide it, the table will not only set the correct limit and offset, but also query the database for the total number of items, and update the paginator with that value.

```php
$paginator = new \Nette\Utils\Paginator();
$paginator->setItemsPerPage(20);
$paginator->setPage(2);

$rows = $table->getAll($orderBy, $paginator);
```

### Insert

To insert a new record into the database table, use the `$table->insert()` method. You have to provide all required values (for columns without a default value) to the method:

```php
$table->insert(
    id: \Ramsey\Uuid\Uuid::uuid4(),
    title: 'Title of the post',
    text: 'Postt text',
    createdAt: \Brick\DateTime\Instant::now(),
    published: true,
);
```

There is also an `$table->insertAndGet()` variant of the method which returns the inserted row:

```php
$article = $table->insertAndGet(/* ... */);
```

### Update

To update a record in the table, use the `$table->update()` method and provide an instance of the table's row or its primary key. You can then use named parameters to specify the values to update:

```php
$table->update(
    ArticlePrimaryKey::from($articleId), // or $articleRow
    deletedAt: \Brick\DateTime\Instant::now(),
);
```

There is also an `$table->updateAndGet()` variant of the method which returns the updated row:

```php
$updatedArticle = $table->updateAndGet($articleRow, /* ... */);
```

#### Bulk update

For convenience, there's also the `$table->updateBy()` method which allows you to perform bulk updates. It requires a set of conditions (like the querying methods), and the values to update:

```php
$table->updateBy(
    [
        $table->published()->is(true),
        $table->createdAt()->is(greaterThan(Instant::now())),
    ]
    published: false,
);
```

### Upsert

To upsert (i.e. insert, or update if it already exists) a new record into the database table, use the `$table->upsert()` method. You have to provide all required values (for columns without a default value) to the method:

```php
$table->upsert(
    id: \Ramsey\Uuid\Uuid::uuid4(),
    title: 'Title of the post',
    text: 'Postt text',
    createdAt: \Brick\DateTime\Instant::now(),
    published: true,
);
```

There is also an `$table->upsertAndGet()` variant of the method which returns the affected row:

```php
$article = $table->upsertAndGet(/* ... */);
```

### Delete

To delete a record, you use the `$table->delete()` method. need its primary key or row:

```php
$table->delete(
    ArticlePrimaryKey::from($articleId), // or $articleRow
);
```

There is also an `deleteAndGet()` variant of the method which returns the deleted row:

```php
$deletedArticle = $table->deleteAndGet(ArticlePrimaryKey::from($articleId));
```

#### Bulk delete

For convenience, there's also the `$table->deleteBy()` method which allows you to perform bulk deletes. It requires a set of conditions (like the querying methods):

```php
$table->deleteBy(
    $table->published()->is(false),
);
```

### Low-level changes

The table also exposes a set of lower-level methods, `new()` and `edit()`. These produce a change set:

```php
$changeSet = $table->new(
    id: \Ramsey\Uuid\Uuid::uuid4(),
    title: 'Title of the post',
    text: 'Postt text',
    createdAt: \Brick\DateTime\Instant::now(),
    published: true,
);
```

or

```php
$changeSet = $table->edit(
    ArticlePrimaryKey::from($articleId), // or $articleRow
    text: 'Post text',
)
```

The change set can further be updated by calling its `modify*()` methods, and eventually saved using the `$table->save()` method:

```php
$changeSet->modifyPublished(false);
$table->save($changeSet);
```


## Type mapping

### Basic types

As you might have noticed, Tables provide default mapping for most PostgreSQL's basic types:

- Textual types (`character`, `character varying`, `text`) all map to `string`.
- Integer types (`smallint`, `int`, `bigint`) all map to `int`.
- Floating-point types (`real`, `double precision`) all map to `float`.
- Boolean type maps to `bool`.
- Binary type (`bytea`) maps to a binary `string`.
- Json types (`json`, `jsonb`) map to a `json_decode()`'d PHP value.

Additional basic types are only mapped provided that certain packages are installed:

- Numeric type (`numeric`/`decimal`) maps to a `BigDecimal` from [brick/math](https://github.com/brick/math).
- Date-time types (`date`, `time`, `timestamp`) map to `LocalDate`, `LocalTime`, and `Instant`, respectively, from [brick/date-time](https://github.com/brick/date-time).
- Uuid type maps to a `Uuid` from [ramsey/uuid](https://github.com/ramsey/uuid).

### Advanced types

In addition to mapping PostgreSQL's basic types by default, Tables let you make the most of the database's complex type system. You can describe and provide mapping for even the wildest combinations of PostgreSQL types.

#### Type resolver

At the core of the type system in Tables is the `TypeResolver`. It decides which type to use for each column based on its database type, or even its scoped name.

You can register your own types in the config file:

```neon
tables:
    types:
        - App\Tables\MyType
        - App\Tables\MyType::decimal(10, 5) # named constructor with parameters
        schema.table.column: App\Tables\MyType
```

You can explicitly map the type to a specific column by using the fully qualified identifier in the item's key (as seen in the second item above.) If you omit the item's key (as seen in the first item above), the type will be registered based on its `getDatabaseType()` and will be used for all columns of that type that do not have an explicit mapping.

Alternatively, you can register implementations of the `TypeResolverConfigurator` interface in the DI container. Tables will automatically pick them up and pass the `TypeResolver` to the configurators's `configure()` method.

#### Custom types

All types implement the `Type` interface and its four methods:

- `getPhpType(): PhpType` returns the scaffolder-compatible type of the represented PHP value;
- `getDatabaseType(): DatabaseType` returns the database type name – this is used when the type is registered using the `TypeResolver::addResolutionByTypeName($type)` method;
- `toDatabase(mixed $value): Dibi\Expression` maps a PHP value of given type to its database representation;
- `fromDatabase(mixed $value): mixed` maps a database representation to its respective PHP value.

This is an example of a custom currency type that maps instances of some `Currency` onto currency codes in the database's `char(3)`:

```php
/**
 * @implements Type<Currency>
 */
final class CurrencyType implements Type
{
	public function getPhpType(): PhpType
	{
		return resolve(Currency::class);
	}

	public function getDatabaseType(): DatabaseType
	{
		return BuiltInType::char();
	}

	public function toDatabase(mixed $value): Expression
	{
		return $value->getCode();
	}

	public function fromDatabase(mixed $value): mixed
	{
		return Currency::of($value);
	}
}
```

There are also a few helpers for creating the most common advanced types:

##### Array types

You can map values to an array via the `ArrayType`. This formats the items using the declared subtype, and serializes them into a PostgreSQL array. Example of an array of dates:

```php
$dateArrayType = ArrayType::of(new DateType());
```

Note that while arrays in PostgreSQL can contain `NULL`, `ArrayType` rejects null values unless they are explicitly allowed:

```php
$nullableDateArrayType = ArrayType::of(NullableType::of(new DateType()));
```

##### Enum types

You can map native PHP enumerations to PostgreSQL's enums using the `EnumType`. This requires that the provided enum is a `\BackedEnum`, and serializes it to its backing value:

```php
enum Status: string {
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}

$statusType = EnumType::of(Status::class, new Database\NamedType(new Database\Identifier('public', 'status')));
```

##### Composite types

There is also a base class for describing composite types:

```php
$moneyType = new class extends CompositeType {
    public function __construct()
    {
        parent::__construct(
            new Database\NamedType(new Database\Identifier('public', 'money')),
            DecimalType::decimal(),
            new CurrencyType(), // custom type from above
        );
    }

    public function getPhpType(): PhpType
    {
        return resolve(Money::class);
    }

    public function toDatabase(mixed $value): Dibi\Expression
    {
        return $this->tupleToDatabase([
            $value->getAmount(),
            $value->getCurrency(),
        ]);
    }

    public function fromDatabase(mixed $value): Money
    {
        [$amount, $currency] = $this->tupleFromDatabase($value);
        return Money::of($amount, $currency);
    }
};
```

Similarly to arrays, in PostgreSQL, composite type fields are always nullable. However, `CompositeType` rejects null values except in positions where they are explicitly allowed:

```php
$moneyType = new class extends CompositeType {
    public function __construct()
    {
        parent::__construct(
            new Database\NamedType(new Database\Identifier('public', 'money')),
            NullableType::of(DecimalType::decimal()),
            new CurrencyType(),
        );
    }

    // ...
}
```
