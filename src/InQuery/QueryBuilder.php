<?php
namespace InQuery;

use InQuery\Query;
use InQuery\Command;

/**
 * Interface all query builders must implement.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
interface QueryBuilder
{
    /**
     * Builds a lookup query command.
     * @param Query $query
     * @return Command
     */
    public function selectQuery(Query $query);
}
