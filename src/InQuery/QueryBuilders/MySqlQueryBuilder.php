<?php
namespace InQuery\QueryBuilders;

use InQuery\QueryBuilder;
use InQuery\Query;
use InQuery\Helpers\MySqlHelper;
use InQuery\Command;
use InQuery\Commands\MysqlCommand;

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
        $fields = [];
        $joins = [];
        $where = [];
        $order = [];
        $tables = [];
        $params = [];
        foreach ($query->getQueryData() as $index => $tableData) {
            $tables[$index] = $tableData[Query::QUERY_SET_TABLE];
            if (count($tableData[Query::QUERY_SET_FIELDS]) === 0) {
                $fields[] = "{$tables[$index]}.*";
            } else {
                foreach ($tableData[Query::QUERY_SET_FIELDS] as $field) {
                    $fields[] = "{$tables[$index]}.{$field}";
                }
            }
            foreach ($tableData[Query::QUERY_SET_WHERE] as $where) {
                $whereVals = MySqlHelper::buildWhere($tables[$index], ...$where);
                $where[] = $whereVals[0];
                $params[$whereVals[1]] = $where[1];
            }
            foreach ($tableData[Query::QUERY_SET_ORDER] as $order) {
                $order[] = "{$tables[$index]}.{$order[0]} {$order[1]}";
            }
            foreach ($tableData[Query::QUERY_SET_JOIN] as $join) {
                $joinStmt = "{$join[2]} {$join[0]} ON";
                $joinConditions = '';
                foreach ($join[1] as $tableColumn => $joinTableColumn) {
                    if (!StringHelper::isEmpty($joinConditions)) {
                        $joinConditions .= " and";
                    }
                    $joinStmt .= " {$tables[$index]}.{$tableColumn} = {$join[2]}.{$joinTableColumn}";
                }
                $joins[] = "{$joinStmt} {$joinConditions}";
            }
        }
        $stmt = "select " . StringHelper::joinLines($fields) 
            . " from " . StringHelper::joinLines($tables) 
            . (!empty($joins) ? ' ' . StringHelper::joinLines($joins) : '')
            . (!empty($where) ? ' where ' . StringHelper::joinLines($where) : '')
            . (!empty($order) ? ' order by ' . StringHelper::joinLines($order) : '');
        return new MySqlCommand(Command::TYPE_FIND, $stmt, $params);
    }
}
