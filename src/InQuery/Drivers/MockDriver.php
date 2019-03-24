<?php
namespace InQuery\Drivers;

use InQuery\Driver;
use InQuery\Drivers\BaseDriver;
use InQuery\Exceptions\DatabaseConnectionException;
use InQuery\Exceptions\DatabaseException;
use InQuery\Exceptions\DependencyException;

/**
 * Mock database driver for tests.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MockDriver extends BaseDriver implements Driver
{
    /**
     * Define constants.
     */
    const NAME = 'mock';

    /**
     * Establishes a connection to the database.
     * @return bool
     * @throws DependencyException
     * @throws DatabaseConnectionException
     */
    public function connect()
    {
        return true;
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
        return ['row1', 'row2'];
    }
}
