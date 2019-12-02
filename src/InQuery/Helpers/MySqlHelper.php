<?php
namespace InQuery\Helpers;

use InQuery\Helpers\StringHelper;
use InQuery\Query;
use InQuery\Command;

/**
 * MySql helper utilities.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MySqlHelper
{
    /**
     * Parses Query elements into sets of usable data.
     * @param Query $query
     * @return array
     */
    public static function parseQueryElements(Query $query)
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
            foreach ($tableData[Query::QUERY_SET_WHERE] as $currentWhere) {
                $whereVals = MySqlHelper::buildWhere($tables[$index], ...$currentWhere);
                $where[] = $whereVals[0];
                $params[$whereVals[1]] = $currentWhere[1];
            }
            foreach ($tableData[Query::QUERY_SET_ORDER] as $currentOrder) {
                $order[] = "{$tables[$index]}.{$currentOrder[0]} {$currentOrder[1]}";
            }
            if (!empty($tableData[Query::QUERY_SET_JOIN])) {
                $joinStmt = "{$tableData[Query::QUERY_SET_JOIN][2]} join {$tableData[Query::QUERY_SET_JOIN][0]} on";
                $joinConditions = '';
                foreach ($tableData[Query::QUERY_SET_JOIN][1] as $tableColumn => $joinTableColumn) {
                    if (!StringHelper::isEmpty($joinConditions)) {
                        $joinConditions .= " and";
                    }
                    $joinStmt .= " {$tables[$index]}.{$tableColumn} = {$tableData[Query::QUERY_SET_JOIN][0]}.{$joinTableColumn}";
                }
                $joins[] = "{$joinStmt} {$joinConditions}";
            }
        }
        return compact('fields', 'joins', 'where', 'order', 'tables', 'params');
    }

    /**
     * Translates where parameters into appropriate where clause.
     * @param string $table
     * @param string $field
     * @param mixed $value
     * @param string $condition (optional)
     * @return array tuple of [whereString, paramName]
     */
    public static function buildWhere($table, $field, $value, $condition = null)
    {
        // if the string is already in the format :{string} it's already a placeholder, honor it
        $paramName = $value;
        if (!(is_string($value) && trim(substr($value, 0, 1)) === ':')) {
            $paramName = self::buildHashParam($table.$field);
        }
        $condition = $condition === null ? Query::EQ : $condition;
        switch ($condition) {
            case Query::IN:
                $whereString = "{$table}.{$field} in ({$paramName})";
                break;
            case Query::NOT_IN:
                $whereString = "{$table}.{$field} not in ({$paramName})";
                break;
            case Query::IS_NULL:
                $whereString = "{$table}.{$field} is null";
                break;
            default:  
                $whereString = "{$table}.{$field} {$condition} {$paramName}";
                break;
        }
        return [$whereString, $paramName];
    }

    /**
     * Builds hash for query parameter.
     * @param string $string
     * @return string
     */
    public static function buildHashParam($string)
    {
        return ':' . StringHelper::hashString($string);
    }

    /**
     * Prepares parameters for the query.
     * @param array $params
     * @return array
     */
    public static function prepareParams(array $params)
    {
        $paramsCopy = $params;
        // foreach ($params as $placeholder => $value) {
        //     if (is_array($value)) {
        //         unset($paramsCopy[$placeholder]);
        //         // need to build up: :placeholder1...:placeholderN parameters
        //         foreach ($value as $index => $tmp) {
        //             $paramsCopy[$placeholder . $index] = $tmp;
        //         }
        //     }
        // }
        return $paramsCopy;
    }
}
