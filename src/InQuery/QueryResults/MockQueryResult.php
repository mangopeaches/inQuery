<?php
namespace InQuery\QueryResults;

use InQuery\QueryResult;

/**
 * Instance of a mock query response.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MockQueryResult implements QueryResult
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
     * Returns results set as an array.
     * @return array
     */
    public function asArray()
    {
        return [
            ['1', 'row1'],
            ['2', 'row2']
        ];
    }
}
