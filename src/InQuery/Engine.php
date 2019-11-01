<?php
namespace InQuery;

use InQuery\Driver;
use InQuery\QueryBuilder;
use InQuery\Query;
use InQuery\Command;
use InQuery\Drivers\MongoDriver;
use InQuery\Drivers\MySqlDriver;
use InQuery\Drivers\MockDriver;
use InQuery\QueryBuilders\MongoQueryBuilder;
use InQuery\QueryBuilders\MySqlQueryBuilder;
use InQuery\QueryBuilders\MockQueryBuilder;
use InQuery\Exceprionts\InvalidDriverException;

/**
 * Engine class that acts as a facade for drivers and query builders.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class Engine implements Driver
{
    /**
     * Define engine types.
     */
    const MYSQL = 'mysql';
    const MONGO = 'mongo';
    const MOCK = 'mock';

    /**
     * Creates a new engine instance.
     * @param string $type engine type
     * @param string $host db host
     * @param int $port db port
     * @param string $username
     * @param string $password
     * @param string $charset
     * @return Engine
     * @throws InvalidDriverException
     */
    public static function create($type, $host, $db, $port = 0, $username = '', $password = '', $charset = '')
    {
        switch ($type) {
            case self::MYSQL:
                return new self(new MySqlDriver($host, $db, $port, $username, $password, $charset), new MySqlQueryBuilder());
            case self::MONGO:
                return new self(new MongoDriver($host, $db, $port, $username, $password, $charset), new MongoQueryBuilder());
            case self::MOCK:
                return new self(new MockDriver($host, $db, $port, $username, $password, $charset), new MockQueryBuilder());
            default:
                throw new InvalidDriverException("Unsupported Engine type {$type} supplied.", InvalidDriverException::INVALID_DRIVER);
        }
    }

    /**
     * Instantiate a new instance.
     * @param Driver $driver
     * @param QueryBuilder $builder
     */
    public function __construct(Driver $driver, QueryBuilder $builder)
    {
        $this->driver = $driver;
        $this->builder = $builder;
    }

    /**
     * Establishes a connection to the database.
     * @throws DatabaseConnectionException
     */
    public function connect()
    {
        $this->driver->connect();
    }

    /**
     * Returns driver's connection intance.
     * @return Resource
     */
    public function getConnection()
    {
        return $this->driver->getConnection();
    }

    /**
     * Returns a new query.
     * @return Query
     */
    public function query()
    {
        return new Query($this->driver, $this->builder);
    }

    /**
     * Executes a Command.
     * @param Command $command
     * @param array $params (optional) command parameters
     * @param int $offset
     * @param int $limit
     * @return QueryResult
     */
    public function exec(Command $command, array $params = [], $offset = self::OFFSET_DEFAULT, $limit = self::RETURNED_ROW_DEFAULT)
    {
        return $this->driver->exec($command, $params, $offset, $limit);
    }
}
