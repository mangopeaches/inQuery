<?php
namespace InQuery;

use InQuery\Driver;
use InQuery\QueryBuilder;
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
class Engine implements Driver, QueryBuilder
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
     * @param string $name name for the connection
     * @param string $host db host
     * @param int $port db port
     * @param string $username
     * @param string $password
     * @return Engine
     * @throws InvalidDriverException
     */
    public static function create($type, $name, $host, $db, $port = 0, $username = '', $password = '')
    {
        switch ($type) {
            case self::MYSQL:
                return new self(new MySqlDriver($host, $db, $port, $username, $password), new MySqlQueryBuilder());
            case self::MONGO:
                return new self(new MongoDriver($host, $db, $port, $username, $password), new MongoQueryBuilder());
            case self::MOCK:
                return new self(new MockDriver($host, $db, $port, $username, $password), new MockQueryBuilder());
            default:
                throw new InvalidDriverException("Unsupported Engine type {$type} supplied.", InvalidDriverException::INVALID_DRIVER);
        }
    }

    /**
     * Instantiate a new instance.
     * @param Driver $driver
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(Driver $driver, QueryBuilder $queryBuilder)
    {
        $this->driver = $driver;
        $this->queryBuilder = $queryBuilder;
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
     * Prepares a find command for execution.
     * @param string $dataSet data set against which to perform the query
     * @param array $conditions (optional) array of query conditions
     * @param array $fields (optional) fields to return, all if omittied
     * @param array $order (optional) fields to order by
     * @param array $options (optional) options to be passed through as query params
     * @return Command
     */
    public function find($dataSet, array $conditions = [], array $fields = [], array $order = [], array $options = [])
    {
        return $this->queryBuilder->find($dataSet, $conditions, $fields, $order, $options);
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
        if (!$this->driver->getConnection()) {
            $this->driver->connect();
        }
        return $this->driver->exec($command, $params, $offset, $limit);
    }
}
