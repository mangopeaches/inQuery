<?php
namespace InQuery;

/**
 * Common interface which all database drivers must implement.
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
    public function find(array $conditions = [], array $fields = [], array $order = [], array $options = [], $offset = self::OFFSET_DEFAULT, $limit = self::RETURNED_ROW_DEFAULT);
}
