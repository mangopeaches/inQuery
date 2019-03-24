<?php
namespace InQuery;

use InQuery\Exceptions\InvalidDriverException;
use InQuery\Driver;

/**
 * Connection class controls the database driver for the respective engine.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class Connection implements Driver
{
    /**
     * Name for the driver.
     * @var string|int
     */
    protected $name = '';
    
    /**
     * Driver instance.
     * @var Driver
     */
    protected $driver = null;

    /**
     * Flag for whether we have already established a connection.
     * @var bool
     */
    protected $connected = false;

    /**
     * Instantiate a new connection.
     * @param string $name connection name
     * @param string $type drive type
     * @param string $host db host
     * @param string $db database name
     * @param int $port (optional) db port number
     * @param string $username (optional)
     * @param string $password (optional)
     * @throws InvalidDriverException
     */
    public function __construct($name, $type, $host, $db, $port = 0, $username = '', $password = '')
    {
        $driver = false;
        // verify the type is a valid driver
        $availableDrivers = scandir(__DIR__ . '/Drivers');
        foreach ($availableDrivers as $driverFile) {
            if (strstr($driverFile, 'Driver.php') !== false &&
                constant('InQuery\\Drivers\\' . str_replace('.php', '', $driverFile) . '::NAME') === $type
            ) {
                $driver = 'InQuery\\Drivers\\' . str_replace('.php', '', $driverFile);
                break;    
            }
        }
        if (!$driver) {
            throw new InvalidDriverException('Invalid driver type ' . $type . ' supplied.', InvalidDriverException::INVALID_DRIVER);
        }
        $this->name = $name;
        $this->driver = new $driver($host, $db, $port, $username, $password);
    }

    /**
     * Returns instance of the db driver.
     * @return Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns the name for the connection.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Established a connection to the database.
     * @throws DatabaseConnectionException
     * @throws DatabaseException
     * @throws DependencyException
     */
    public function connect()
    {
        $this->driver->connect();
    }

    /**
     * Queries for records from the database.
     * @param array $conditions (optional) array of query conditions
     * @param array $fields (optional) fields to return, all if omittied
     * @param array $order (optional) fields to order by
     * @param array $options (optional) options to be passed through as query params
     * @param int $offset (optional) rows to skip
     * @param int $limit (optional) number of rows to return
     * @return TBD
     * @throws DatabaseConnectionException
     * @throws DatabaseException
     */
    public function find(array $conditions = [], array $fields = [], array $order = [], array $options = [], $offset = self::OFFSET_DEFAULT, $limit = self::RETURNED_ROW_DEFAULT)
    {
        $this->driver->find($conditions, $fields, $order, $options, $offset, $limit);
    }
}
