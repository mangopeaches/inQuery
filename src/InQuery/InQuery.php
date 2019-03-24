<?php
namespace InQuery;

use InQuery\Exceptions\InvalidParamsException;
use InQuery\Exceptions\SetupException;
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

        foreach ($params as $index => $paramSet) {
            if (empty($paramSet['driver']) || empty($paramSet['host']) || empty($paramSet['db'])) {
                throw new InvalidParamsException('You must supply at lease a \'driver\', \'host\', and \'db\' for each connection.', InvalidParamsException::INIT_PARAMS_INVALID);
            }
            $name = isset($paramsSet['name']) ? $paramsSet['name'] : $index;
            $port = isset($paramSet['port']) ? $paramSet['port'] : 0;
            $username = isset($paramSet['username']) ? $paramSet['username'] : '';
            $password = isset($paramSet['password']) ? $paramSet['password'] : '';
            if (isset($paramsSet['default']) && $paramsSet['default'] === true) {
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
     * Returns instance of the default driver.
     * @return Driver
     */
    public function getDriver()
    {
        return $this->conn[0]->getDriver();
    }

    /**
     * Handle attempts to access a database connection by name.
     * @param string $name connection name
     * @return Driver
     * @throws InvalidConnectionException when name not found
     */
    public function __get($name)
    {
        if (ctype_digit($name) && count($this->conn) >= $name) {
            return $this->conn[(int)$name]->getDriver();
        }
        foreach ($this->conn as $connection) {
            if ($connection->getName() === $name) {
                return $connection->getDriver();
            }
        }
        throw new InvalidConnectionException('Connection ' . $name . ' was not found in connection set.', InvalidConnectionException::INVALID_CONNECTION);
    }
}
