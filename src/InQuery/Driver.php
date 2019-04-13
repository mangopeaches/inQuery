<?php
namespace InQuery;

use InQuery\QueryResult;
use InQuery\Exceptions\DatabaseConnectionException;
use InQuery\Command;

/**
 * Interface which all database drivers must implement.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
interface Driver
{
    /**
     * Define constants.
     */
    const OFFSET_DEFAULT = -1;
    const RETURNED_ROW_DEFAULT = -1;

    /**
     * Establishes a connection to the database.
     * @throws DatabaseConnectionException
     */
    public function connect();

    /**
     * Returns connection instance.
     * @return Resource
     */
    public function getConnection();

    /**
     * Executes query and returns result.
     * @param Command $command
     * @param array $params
     * @param int $offset
     * @param int $limit
     * @return QueryResult
     */
    public function exec(Command $command, array $params = [], $offset = self::OFFSET_DEFAULT, $limit = self::RETURNED_ROW_DEFAULT);
}
