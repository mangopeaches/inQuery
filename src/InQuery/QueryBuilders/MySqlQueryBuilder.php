<?php
namespace InQuery\QueryBuilders;

use InQuery\QueryBuilder;
use InQuery\Query;
use InQuery\Helpers\MySqlHelper;
use InQuery\Helpers\StringHelper;
use InQuery\Command;
use InQuery\Commands\MySqlCommand;

/**
 * Translates input values into mysql commands.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MySqlQueryBuilder implements QueryBuilder
{
    /**
     * Builds a lookup query command.
     * @param Query $query
     * @return Command
     */
    public function selectQuery(Query $query)
    {
        $queryElements = MySqlHelper::parseQueryElements($query);
        $stmt = "select " . StringHelper::joinLines($queryElements['fields'], ', ') 
            . " from " . $queryElements['tables'][0] 
            . (!empty($queryElements['joins']) ? ' ' . StringHelper::joinLines($queryElements['joins']) : '')
            . (!empty($queryElements['where']) ? ' where ' . StringHelper::joinLines($queryElements['where'], ' and ') : '')
            . (!empty($queryElements['order']) ? ' order by ' . StringHelper::joinLines($queryElements['order'], ', ') : '');
        return new MySqlCommand(Command::TYPE_FIND, $stmt, $queryElements['params']);
    }

    /**
     * Builds an insert query and returns the command.
     * @param Query $query
     * @return Command
     */
    public function insertQuery(Query $query)
    {
        return new MySqlCommand(Command::TYPE_INSERT, $stmt, $params);
    }

    /**
     * Builds a delete query and returns the command.
     * @param Query $query
     * @return Command
     */
    public function deleteQuery(Query $query)
    {
        $queryElements = MySqlHelper::parseQueryElements($query);
        $stmt = "delete from " . $queryElements['tables'][0] 
            . (!empty($queryElements['joins']) ? ' ' . StringHelper::joinLines($queryElements['joins']) : '')
            . (!empty($queryElements['where']) ? ' where ' . StringHelper::joinLines($queryElements['where'], ' and ') : '');
        return new MySqlCommand(Command::TYPE_DELETE, $stmt, $queryElements['params']);
    }
}
