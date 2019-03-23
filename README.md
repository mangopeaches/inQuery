# inQuery
Straightforward PHP Database Driver

## Setup

The first thing you need to do is provide your connection parameters.

To do so, simple instantiate a new instance with your connection parameters, like such:\
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
*Note*: When you supply multiple connections, you _should_ supply a 'default' element to indicate which connection you want to use as the default when one is not explicitly specified.\
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
])
```