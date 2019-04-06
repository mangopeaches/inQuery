<?php
namespace InQuery\QueryBuilders;

use InQuery\QueryBuilder;
use InQuery\Command;
use InQuery\Commands\MockCommand;

/**
 * Translates input values into mock format.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MockQueryBuilder implements QueryBuilder
{
    /**
     * Prepares a find command for execution.
     * @param string $dataSet data set against which to perform the query
     * @param array $conditions (optional) array of query conditions
     * @param array $fields (optional) fields to return, all if omittied
     * @param array $order (optional) fields to order by
     * @param array $options (optional) options to be passed through as query params
     * @return Command
     */
    public function find($dataSet, array $conditions = [], array $fields = [], array $order = [], array $options = [])
    {
        return new MockCommand(Command::TYPE_FIND, 'lolcommand');
    }
}
