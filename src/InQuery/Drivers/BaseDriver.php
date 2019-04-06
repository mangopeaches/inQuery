<?php
namespace InQuery\Drivers;

use InQuery\Driver;

/**
 * Base database driver.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
abstract class BaseDriver implements Driver
{
    /**
     * Define class constants.
     */
    const NAME = 'base';

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
     * Returns connection instance.
     * @return Resource
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
