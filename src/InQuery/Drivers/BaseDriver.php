<?php
namespace InQuery\Drivers;

use InQuery\Driver;
use InQuery\Command;
use InQuery\QueryResult;

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
     * Character set.
     * @var string
     */
    protected $charset = '';

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
     * @param string $charset (optinal)
     */
    public function __construct($host, $db, $port = 0, $username = '', $password = '', $charset = '')
    {
        $this->host = $host;
        $this->db = $db;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
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
