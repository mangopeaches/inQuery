<?php
namespace InQuery;

use InQuery\QueryResult;

/**
 * Instance of a mongo query response.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MongoQueryResult implements QueryResult
{
    /**
     * Count of rows returned.
     * @var int
     */
    protected $count = 0;

    /**
     * Instantiate a new instance.
     */
    public function __construct()
    {

    }

    /**
     * Return count of returned rows.
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Returns full result set as an array.
     * @return array
     */
    public function asArray()
    {
        return [];
    }
}
