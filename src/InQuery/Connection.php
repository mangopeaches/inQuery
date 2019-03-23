<?php
namespace InQuery;

use InQuery\Exceptions\InvalidDriverException;
use InQuery\Driver;

/**
 * Connection class controls the database driver for the respective engine.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class Connection
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
                constant('InQuery\\Drivers\\' . str_replace('.php', '', $driverFile), '::NAME') === $type
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
     * Establishes a database connection.
     * @return bool whether we established a connection successfully
     * @throws DatabaseConnectException
     */
    public function connect()
    {
        if (!$this->connected) {
            return $this->driver->connect();
        }
        return true;
    }
}
