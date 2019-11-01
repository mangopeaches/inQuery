<?php
namespace InQuery\Drivers;

use InQuery\Driver;
use InQuery\Command;
use InQuery\Drivers\BaseDriver;
use InQuery\Queries\FindQuery;
use InQuery\Helpers\MySqlHelper;
use InQuery\Exceptions\DatabaseConnectionException;
use InQuery\Exceptions\DatabaseException;
use InQuery\QueryResults\MySqlQueryResult;

/**
 * MySql database driver.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MySqlDriver extends BaseDriver implements Driver
{
    /**
     * Define constants.
     */
    const NAME = 'mysql';

    /**
     * Establishes a connection to the database.
     * @return bool
     * @throws DependencyException
     * @throws DatabaseConnectionException
     */
    public function connect()
    {
        // first need to check the client is installed
        if (!extension_loaded('mysqlnd')) {
            throw new DependencyException('PDO driver is not installed.', DependencyException::MISSING_PDO_DRIVER);
        }
        $user = !empty($this->username) ? $this->username : 'root';
        $pass = !empty($this->password) ? $this->password : '';
        $port = !empty($this->port) ? $this->port : 3306;
        $charset = !empty($this->charset) ? $this->charset : 'utf8mb4';
        
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$charset}";
        try {
            $this->connection = new \PDO($dsn, $user, $pass, [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ]);
        } catch (\Exception $e) {
            throw new DatabaseConnectionException('Failed to establish a connection to the database.', DatabaseConnectionException::CONNECT_ERROR, $e);
        }
    }

    /**
     * Executes query and returns result.
     * @param Command $command
     * @param array $params
     * @param int $offset
     * @param int $limit
     * @return QueryResult
     */
    public function exec(Command $command, array $params = [], $offset = self::OFFSET_DEFAULT, $limit = self::RETURNED_ROW_DEFAULT)
    {
        if ($this->connection === null) {
            $this->connect();
        }

        $params = MySqlHelper::prepareParams(array_merge($command->getParams(), $params));
        try {
            $stmt = $this->connection->prepare($command->getCommand(), [\PDO::ATTR_CURSOR]);
            $stmt->execute($params);
            return new MySqlQueryResult($stmt);
        } catch (\PDOException $e) {
            throw new DatabaseException('A database error occurred.', DatabaseException::FIND_EXCEPTION, $e);
        }
    }
}
