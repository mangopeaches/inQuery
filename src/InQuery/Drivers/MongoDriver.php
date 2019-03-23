<?php
namespace InQuery\Drivers;

use InQuery\Driver;
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
     * @return bool
     * @throws DependencyException
     * @throws DatabaseConnectionException
     */
    public function connect()
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
     * @return TBD
     */
    public function find(array $conditions = [], array $fields = [], array $order = [], array $options = [], $offset = Driver::OFFSET_DEFAULT, $limit = Driver::RETURNED_ROW_DEFAULT)
    {
        parent::find($conditions, $fields, $order, $options, $offeset, $limit);
        return ['row1', 'row2'];
    }
}
