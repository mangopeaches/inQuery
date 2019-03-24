<?php
namespace InQuery;

use InQuery\Exceptions\InvalidParamsException;
use InQuery\Exceptions\SetupException;
use InQuery\Exceptions\InvalidConnectionException;
use InQuery\Connection;

/**
 * Container class for the application.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
final class InQuery
{
    /**
     * Instance of self.
     * @var InQuery|null
     */
    private static $instance = null;

    /**
     * DB connections.
     * @var Connection[]
     */
    private $conns = [];

    /**
     * Instantiate a new instance.
     * @param array $params
     * @throws InvalidParamsException
     * @throws InvalidDriverException
     */
    private function __construct(array $params)
    {
        if (count($params) === 0) {
            throw new InvalidParamsException('Invalid connection parameters supplied to init function.', InvalidParamsException::INIT_PARAMS_INVALID);
        }

        // if we just have a config array, convert to an array of arrays for simplicity
        if (!isset($params[0])) {
            $params = [$params];
        }

        foreach ($params as $index => $paramSet) {
            if (empty($paramSet['driver']) || empty($paramSet['host']) || empty($paramSet['db'])) {
                throw new InvalidParamsException('You must supply at lease a \'driver\', \'host\', and \'db\' for each connection.', InvalidParamsException::INIT_PARAMS_INVALID);
            }
            $name = isset($paramSet['name']) ? $paramSet['name'] : $index;
            $port = isset($paramSet['port']) ? $paramSet['port'] : 0;
            $username = isset($paramSet['username']) ? $paramSet['username'] : '';
            $password = isset($paramSet['password']) ? $paramSet['password'] : '';
            if (isset($paramSet['default']) && $paramSet['default'] === true) {
                array_unshift($this->conns, new Connection($name, $paramSet['driver'], $paramSet['host'], $paramSet['db'], $port, $username, $password));
            } else {
                array_push($this->conns, new Connection($name, $paramSet['driver'], $paramSet['host'], $paramSet['db'], $port, $username, $password));
            }
        }
    }

    /**
     * Initialization function to setup the app.
     * @param array $params
     * @throws InvalidParamsException
     * @throws InvalidDriverException
     */
    public static function init(array $params)
    {
        if (self::$instance === null) {
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    /**
     * Returns current instance.
     * @throws SetupException
     * @return InQuery
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            throw new SetupException('You have not initialized InQuery.', SetupException::GET_INSTANCE_ERROR);
        }
        return self::$instance;
    }

    /**
     * Returns instance of the connection.
     * @return Connection
     */
    public function getConnection()
    {
        return $this->conns[0];
    }

    /**
     * Handle attempts to access a database connection by name.
     * @param string $name connection name
     * @return Connection
     * @throws InvalidConnectionException when name not found
     */
    public function __get($name)
    {
        if (ctype_digit($name) && count($this->conns) >= $name) {
            return $this->conns[(int)$name];
        }
        foreach ($this->conns as $connection) {
            if ($connection->getName() === $name) {
                return $connection;
            }
        }
        throw new InvalidConnectionException('Connection ' . $name . ' was not found in connection set.', InvalidConnectionException::INVALID_CONNECTION);
    }
}
