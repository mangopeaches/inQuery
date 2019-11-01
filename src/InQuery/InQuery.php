<?php
namespace InQuery;

use InQuery\Exceptions\InvalidParamsException;
use InQuery\Exceptions\SetupException;
use InQuery\Exceptions\InvalidConnectionException;
use InQuery\Engine;

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
     * @var Engine[]
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
            if (empty($paramSet['engine']) || empty($paramSet['host']) || empty($paramSet['db'])) {
                throw new InvalidParamsException('You must supply an \'engine\', \'host\', and \'db\' for each connection.', InvalidParamsException::INIT_PARAMS_INVALID);
            }
            $name = isset($paramSet['name']) ? $paramSet['name'] : $index;
            $port = isset($paramSet['port']) ? $paramSet['port'] : 0;
            $username = isset($paramSet['username']) ? $paramSet['username'] : '';
            $password = isset($paramSet['password']) ? $paramSet['password'] : '';
            $charset = isset($paramSet['charset']) ? $paramSet['charset'] : '';
            $default = isset($paramSet['default']) && $paramSet['default'] === true;
            $this->conns[] = [
                'name' => $name,
                'default' => $default,
                'engine' => Engine::create($paramSet['engine'], $paramSet['host'], $paramSet['db'], $port, $username, $password, $charset)
            ];
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
     * @return Engine
     */
    public function getConnection()
    {
        // return the default if one is set, otherwise default to first in the set    
        foreach ($this->conns as $conn) {
            if ($conn['default'] === true) {
                return $conn['engine'];
            }
        }
        return $this->conns[0]['engine'];
    }

    /**
     * Handle attempts to access a database connection by name.
     * @param string $name connection name
     * @return Engine
     * @throws InvalidConnectionException when name not found
     */
    public function __get($name)
    {
        foreach ($this->conns as $conn) {
            if ($conn['name'] == $name) {
                return $conn['engine'];
            }
        }
        throw new InvalidConnectionException('Connection ' . $name . ' was not found in connection set.', InvalidConnectionException::INVALID_CONNECTION);
    }
}
