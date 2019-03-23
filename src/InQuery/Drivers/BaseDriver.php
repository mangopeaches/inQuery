<?php
namespace InQuery\Drivers;

use InQuery\Driver;
use InQuery\Exceptions\DatabaseConnectionException;
use InQuery\Exceptions\DatabaseException;
use InQuery\Exceptions\DependencyException;

/**
 * Base database driver.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
abstract class BaseDriver implements Driver
{
    /**
     * Database host.
     * @var string
     */
    protected $host = '';

    /**
     * Database name.
     * @var string
     */
    protected $db = '';

    /**
     * Database port.
     * @var int
     */
    protected $port = 0;

    /**
     * Username for db user.
     * @var string
     */
    protected $username = '';

    /**
     * Password for db user.
     * @var string
     */
    protected $password = '';

    /**
     * Intance of the database connection.
     * @var mixed
     */
    protected $connection = null;

    /**
     * Instantiate a new instance.
     * @param string $host db host
     * @param string $db database name
     * @param int $port (optional)
     * @param string $username (optional)
     * @param string $password (optional)
     */
    public function __construct($host, $db, $port = 0, $username = '', $password = '')
    {
        $this->host = $host;
        $this->db = $db;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Queries for records from the database.
     * @param array $conditions (optional) array of query conditions
     * @param array $fields (optional) fields to return, all if omittied
     * @param array $order (optional) fields to order by
     * @param array $options (optional) options to be passed through as query params
     * @param int $offset (optional) rows to skip
     * @param int $limit (optional) number of rows to return
     * @throws DatabaseConnectionException
     * @throws DatabaseException
     * @throws DependencyException;
     * @return TBD
     */
    public function find(array $conditions = [], array $fields = [], array $order = [], array $options = [], $offset = Driver::OFFSET_DEFAULT, $limit = Driver::RETURNED_ROW_DEFAULT)
    {
        if ($this->connection === null) {
            $this->connect();
        }
    }
}
