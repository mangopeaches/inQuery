# inQuery

[![Build Status](https://travis-ci.org/mangopeaches/inQuery.svg?branch=master)](https://travis-ci.org/mangopeaches/inQuery)

Straightforward PHP Database Driver

## Setup

The first thing you need to do is provide your connection parameters.

To do so, simple instantiate a new instance with your connection parameters, like such:
```php
$db = InQuery\InQuery::init([
    'name' => 'default',
    'driver' => InQuery\Driver::MONGO,
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
        'driver' => InQuery\Drivers\MongoDriver::NAME,
        'host' => 'localhost',
        'port' => '27017',
        'db' => 'example',
        'username' => 'username',
        'password' => 'password'
    ],
    [
        'default' => false,
        'name' => 'db2',
        'driver' => InQuery\Drivers\MySqlDriver::MYSQL,
        'host' => 'localhost',
        'port' => '3306',
        'db' => 'example',
        'username' => 'username',
        'password' => 'password'
    ]
]);
```

## Get an instance of the db driver

Once you have established a connection you can get an instance of your database drivers in two ways.

### Default driver

This will return your default driver, or if you only have one driver defined, your only driver.

```php
// ... assuming you have $db from above
$driver = $db->getDriver();
// OR
$driver = InQuery\InQuery::getInstance()->getDriver();
```

### Returning specials driver (when more than one)

This would return the driver for connection named 'db2' in the above example with 2 drivers.
```php
// ...assuming you have $db from above
$db2Driver = $db->db2->getDriver();
// OR
$db2Driver = InQuery\InQuery::getInstance()->db2->getDriver();
```
## Querying the DB

Queries can be performed against the driver directly or the connection object, which will delegate to the driver itself.

*Note*: The driver actually never establishes a connection to the database until the first read or write operation is requested. This saves the extra overhead of establishing unnecessary connections that aren't used, but means that all read and write options can potentially throw the same `DatabaseConnectionException`, `DependencyConnections`, and `DatabaseException` execptions. It's always good practive to wrap these actions in a try/catch block for your safety.

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
$driver = $db->getDriver();

// query the driver with the table name and query params
try {
    // finds username, firstName, lastName, and lastLoggedIn fields for all logged in users
    // orders by lastLoggedIn desc, so most recent logins
    $result = $driver->find('users',
    ['loggedIn', '=', true],
    ['username', 'firstName', 'lastName', 'lastLoggedIn'], 
    ['lastLoggedIn', -1]);
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
