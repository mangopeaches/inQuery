<?php
namespace InQuery\QueryResults;

use InQuery\QueryResult;

/**
 * Instance of a mysql query response.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MySqlQueryResult implements QueryResult, \IteratorAggregate
{
    /**
     * Count of rows returned.
     * @var int
     */
    protected $count = 0;

    /**
     * Instance of result set.
     * @var \PDOStatement
     */
    protected $result;

    /**
     * Current position in result set.
     * @var int
     */
    protected $position = 0;

    /**
     * Instantiate a new instance.
     * @param \PDOStatement $result
     */
    public function __construct(\PDOStatement $result)
    {
        $this->result = $result;
        $this->position = 0;
    }

    /**
     * Returns object which can be iterated.
     * @return \PDOStatement
     */
    public function getIterator()
    {
        return $this->result;
    }

    /**
     * Return count of returned rows.
     * @return int
     */
    public function count()
    {
        return $this->result->rowCount();
    }

    /**
     * Returns full result set as an array.
     * @return array
     */
    public function asArray()
    {
        return $this->result->fetchAll();
    }
}
