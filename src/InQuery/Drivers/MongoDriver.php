<?php
namespace InQuery\Drivers;

use InQuery\Driver;
use InQuery\{QueryResult};
use InQuery\Drivers\BaseDriver;
use InQuery\Exceptions\DatabaseConnectionException;
use InQuery\Exceptions\DatabaseException;
use InQuery\Exceptions\DependencyException;

/**
 * MongoDB database driver.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MongoDriver extends BaseDriver implements Driver
{
    /**
     * Define constants.
     */
    const NAME = 'mongo';

    /**
     * Establishes a connection to the database.
     * @return void
     * @throws DependencyException
     * @throws DatabaseConnectionException
     */
    public function connect(): void
    {
        // first need to check the client is installed
        if (!extendion_loaded('mongo')) {
            throw new DependencyException('MongoDB driver is not installed.', DependencyException::MISSING_MONGO_DRIVER);
        }
        $options = !empty($this->username) && !empty($this->password) ? ['username' => $this->username, 'password' => $this->password] : [];
        $host = strstr($this->host, 'mongodb://') ? $this->host : 'mongodb://' . $this->host;
        $port = !empty($this->port) ? $this->port : 27017;
        // use credentials if supplied
        try {
            $this->connection = new \MongoDB\Client($host . ':' . $port . '/' . $this->db, $options);
            $this->connection->{$this->db};
        } catch (\Exception $e) {
            throw new DatabaseConnectionException('Failed to establish a connection to the database.', DatabaseConnectionException::CONNECT_ERROR, $e);
        }
    }
}
