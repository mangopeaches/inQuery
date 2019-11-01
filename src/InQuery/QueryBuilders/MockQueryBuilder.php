<?php
namespace InQuery\QueryBuilders;

use InQuery\QueryBuilder;
use InQuery\Query;
use InQuery\Command;
use InQuery\Commands\MockCommand;

/**
 * Translates input values into mock format.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MockQueryBuilder implements QueryBuilder
{
    /**
     * Builds a select query.
     * @param Query $query
     * @return Command
     */
    public function selectQuery(Query $query)
    {
        return new MockCommand(Command::TYPE_FIND, "select * from bananas", []);
    }

    public function insertQuery(Query $query)
    {
        return new MockCommand(Command::TYPE_INSERT, "insert into blah", []);
    }
}
