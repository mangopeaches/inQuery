<?php
namespace InQuery;

/**
 * Instance of a query response.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
interface QueryResult
{
    /**
     * Return count of returned rows.
     * @return int
     */
    public function count();

    /**
     * Returns the full query set as an array.
     * @return array
     */
    public function asArray();
}
