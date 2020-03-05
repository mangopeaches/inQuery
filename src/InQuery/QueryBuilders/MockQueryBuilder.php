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

    /**
     * Builds an insert query.
     * @param Query $query
     * @return Command
     */
    public function insertQuery(Query $query)
    {
        return new MockCommand(Command::TYPE_INSERT, "insert into blah", []);
    }

    /**
     * Builds a delete query.
     * @param Query $query
     * @return Command
     */
    public function deleteQuery(Query $query)
    {
        return new MockCommand(Command::TYPE_DELETE, "delete from blah", []);
    }

    /**
     * Builds an update query.
     * @param Query $query
     * @return Command
     */
    public function updateQuery(Query $query)
    {
        return new MockQuery(Command::TYPE_UPDATE, "update someTable", []);
    }
}
