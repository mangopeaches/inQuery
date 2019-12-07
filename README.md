# inQuery

[![Build Status](https://travis-ci.org/mangopeaches/inQuery.svg?branch=master)](https://travis-ci.org/mangopeaches/inQuery)

Straightforward PHP Database Driver

## Setup

The first thing you need to do is provide your connection parameters.

To do so, simple instantiate a new instance with your connection parameters, like such:
```php
$db = InQuery\InQuery::init([
    'name' => 'default',
    'engine' => InQuery\Engine::MONGO,
    'host' => 'localhost',
    'port' => '27017',
    'db' => 'example',
    'username' => 'username',
    'password' => 'password'
]);
```

Or you can establish multiple connections at once:\
*Note*: When you supply multiple connections, you _should_ supply a 'default' element to indicate which connection you want to use as the default when one is not explicitly specified.
```php
$db = InQuery\InQuery::init([
    [
        'default' => true,
        'name' => 'db1',
        'engine' => InQuery\Engine::MONGO,
        'host' => 'localhost',
        'port' => '27017',
        'db' => 'example',
        'username' => 'username',
        'password' => 'password'
    ],
    [
        'default' => false,
        'name' => 'db2',
        'engine' => InQuery\Engine::MYSQL,
        'host' => 'localhost',
        'port' => '3306',
        'db' => 'example',
        'username' => 'username',
        'password' => 'password'
    ]
]);
```

## Get an instance of the db driver

Once you have established a connection you can get an instance of your database driver in two ways.

### Default driver

This will return your default driver, or if you only have one driver defined, your only driver.

```php
// ... assuming you have $db from above
$driver = $db->getConnection();
// OR
$driver = InQuery\InQuery::getInstance()->getConnection();
```

### Returning specific driver (when more than one)

This would return the driver for connection named 'db2' in the above example with 2 drivers.
```php
// ...assuming you have $db from above
$db2Driver = $db->db2;
// OR
$db2Driver = InQuery\InQuery::getInstance()->db2;
```

## Late Connection Establishment

*Note*: The driver actually never establishes a connection to the database until the first operation is requested. This saves the extra overhead of establishing unnecessary connections that aren't used, but means that all read and write options can potentially throw the same `DatabaseConnectionException`, `DependencyConnections`, and `DatabaseException` exceptions. It's always good practive to wrap these actions in a try/catch block for your safety.

## Select Queries

The pattern for querying is as such:
* First instantiate a new query via `query()`
* Specify the table you're querying against with `table('tableName')`
* (optional) Specify tables to join via the `join()` command
* (optional) Specify table column names, as a list `select('column1', 'column2')`
* (optional) Specify where conditions via the `where()` command
* (optional) Specify order conditions via the `order()` command
* Execute the query via `get()` with an optional array of query parameters and, optionally, offset and limit parameters


### Get Definition
```php
/**
 * Execute the select query.
 * @param array $params
 * @param int $offset
 * @param int $limit
 * @return QueryResult
 * @throws 
 */
public function get(array $params = [], $offset = Driver::OFFSET_DEFAULT, $limit = Driver::RETURNED_ROW_DEFAULT);
```

### Example Usage
```php
$driver->query()
    ->table('test')
    ->select('column1', 'column2')
    ->where('column1', ':value')
    ->order('column1', Query::ORDER_DESC)
    ->get([':value' => 'test']);
```

## Joining Tables
```php
/**
 * Defines a join.
 * @param string $table
 * @param array $on
 * @param string $type
 * @return $this
 * @throws InvalidJoinException
 */
public function join($table, array $on, $type = self::JOIN_INNER);
```

The `join` command table a table name, of the table to join on, an array of join conditions, in the format:
```php
join('joinedTable', ['parentTableColumn' => 'joinedTableColumn', ...])
```
You can specify multiple columns to join on as well, all of which will be combined with AND.

`join` also takes an optional third parameter, representing the join type. The following types are available:
```php
Query::JOIN_INNER // inner join
Query::JOIN_LEFT // left
Query::JOIN_RIGHT // right
Query::JOIN_OUTER // outer
Query::JOIN_RIGHT_OUTER // right outer
Query::JOIN_LEFT_OUTER // left outer
```

## Delete Queries

The pattern for delete queries is as such:
* First instantiate a new query via `query()`
* Specify the table from which you would like to delete via `table('tableName')`
* (optional) Specify tables to join via the `join()` command
* (optional) Specify where conditions via the `where()` command
* Execute the query via the `delete()` command with an optional set of query parameters

```php
/**
 * Execute the delete query.
 * @param array $params
 * @param int $offset
 * @param int $limit
 * @return QueryResult
 * @throws 
 */
public function get(array $params = [], $offset = Driver::OFFSET_DEFAULT, $limit = Driver::RETURNED_ROW_DEFAULT);
```

## Insert Queries

The pattern for insert queries is as such:
* First instantiate a new query via `query()`
* Specify the table from which you would like to insert via `table('tableName')`
* Specify the column names which you would like to insert via the `columns()` command
* (optionally) Specify update params on duplicate key collision via `onDuplicateKeyUpdate()` command
* Execute the query via the `insert()` command

```php
/**
 * Execute an insert query.
 * @param array $params
 * @return QueryResult
 * @throws
 */
public function insert(array $params);
```

### On Duplicate Key Update

If you elect to perform an ON DUPLICATE KEY UPDATE on your insert statement, you have a few options.

You can choose to update a column with the updated value you are currently inserting by doing the following manner (this example assumes lastName is a unique index, and will update the previous 'Smith' lastName record to have the firstName 'John')
```php
$driver->query()->table('user')->columns('firstName', 'lastName')->onDuplicateKeyUpdate(['firstName' => Query::DUPLIDATE_KEY_UPDATE])->insert(['John', 'Smith']);
```

You can also use a literal value, if you would like (in this example, assume the unique key is lastName. This would always explicitly update the age column to 25 whenever you try to insert a 'Smith' lastName. Apparently Smith's are perpetually 25 years old??):
```php
$driver->query()->table('user')->columns('firstName', 'lastName', 'age')->onDuplicateKeyUpdate(['age' => 15])->insert(['John', 'Smith', 25]);
```

Additionally, you can choose a more expressive options. In this example, on duplicate key, you'll add the insertion values of a and b and store their sum in column c):
```php
$driver->query()->table('test')->columns('a', 'b', 'c')->onDuplicateKeyUpdate([
    'c' => [
        Query::OPERATION_ADD, [
            'a' => Query::DUPLICATE_KEY_UPDATE,
            'b' => Query::DUPLICATE_KEY_UPDATE
        ]
    ]
])->insert([2, 4, 4]);
```

In that example, your resulting rown would end up like:
```sql
|   a   |   b   |   c   |
|   2   |   4   |   6   |   
```


## Full Sample

Queries can be performed against the driver directly or the connection object, which will delegate to the driver itself.

```php
<?php

use InQuery\InQuery;
use InQuery\Exceptions\DatabaseConnectionException;
use InQuery\Exceptions\DatabaseException;
use InQuery\Exceptions\DependencyException;

// initialize db instance
$db = InQuery::init([
    'default' => true,
    'name' => 'db1',
    'driver' => InQuery\Drivers\MongoDriver::NAME,
    'host' => 'localhost',
    'port' => '27017',
    'db' => 'example',
    'username' => 'username',
    'password' => 'password'
]);

// get driver
$driver = $db->getConnection();

// query the driver with the table name and query params
try {
    $results = $driver->query()
        ->table('test')
        ->where('column1', ':value')
        ->delete([':value' => 'test']);
} catch (DependencyException $e) {
    // you don't have the driver installed
    echo $e->getMessage() . var_export($e, true);
} catch (DatabaseConnectionException $e) {
    // could not establish a connection to the database
    echo $e->getMessage() . var_export($e, true);
} catch (DatabaseException $e) {
    // there was a database exception from the underlying db driver
    echo $e->getMessage() . var_export($e, true);
}
```
