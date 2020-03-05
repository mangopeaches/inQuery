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
     * Parses Query elements for select queries into sets of usable data.
     * @param Query $query
     * @return array
     */
    public static function parseSelectQueryElements(Query $query)
    {
        $data = [
            'fields' => [],
            'joins' => [],
            'where' => [],
            'order' => [],
            'tables' => [],
            'params' => []
        ];
        foreach ($query->getQueryData() as $index => $tableData) {
            $data['tables'][$index] = $tableData[Query::QUERY_SET_TABLE];
            self::buildFields($data['tables'][$index], $tableData[Query::QUERY_SET_FIELDS], $data['fields']);
            self::buildWhere($data['tables'][$index], $tableData[Query::QUERY_SET_WHERE], $data['where'], $data['params']);
            self::buildOrder($data['tables'][$index], $tableData[Query::QUERY_SET_ORDER], $data['order']);
            if (!empty($tableData[Query::QUERY_SET_JOIN])) {
                self::buildJoin($data['tables'][$index], $tableData[Query::QUERY_SET_JOIN], $data['joins']);
            }
        }
        return $data;
    }

    /**
     * Parses Query elements for delete queries into usable data.
     * @param Query $query
     * @return array
     */
    public static function parseDeleteQueryElements(Query $query)
    {
        $data = [
            'joins' => [],
            'where' => [],
            'tables' => [],
            'params' => []
        ];
        foreach ($query->getQueryData() as $index => $tableData) {
            $data['tables'][$index] = $tableData[Query::QUERY_SET_TABLE];
            self::buildWhere($data['tables'][$index], $tableData[Query::QUERY_SET_WHERE], $data['where'], $data['params']);
            if (!empty($tableData[Query::QUERY_SET_JOIN])) {
                self::buildJoin($data['tables'][$index], $tableData[Query::QUERY_SET_JOIN], $data['joins']);
            }
        }
        return $data;
    }

    /**
     * Parses query elements for update queries into usable data.
     * @param Query $query
     * @return array
     */
    public static function parseUpdateQueryElements(Query $query)
    {
        $data = [
            'columns' => [],
            'joins' => [],
            'where' => [],
            'tables' => [],
            'params' => [],
            'sets' => []
        ];
        foreach ($query->getQueryData() as $index => $tableData) {
            $data['tables'][$index] = $tableData[Query::QUERY_SET_TABLE];
            self::buildWhere($data['tables'][$index], $tableData[Query::QUERY_SET_WHERE], $data['where'], $data['params']);
            self::buildSet($data['tables'][$index], $tableData[Query::QUERY_SET_SET], $data['sets'], $data['params']);
            if (!empty($tableData[Query::QUERY_SET_JOIN])) {
                self::buildJoin($data['tables'][$index], $tableData[Query::QUERY_SET_JOIN], $data['joins']);
            }
        }
        return $data;
    }

    /**
     * Parses Query elements for insert queries into usable data.
     * @param Query $query
     * @return array
     */
    public static function parseInsertQueryElements(Query $query)
    {
        $data = [
            'columns' => [],
            'placeholders' => [],
            'tables' => [],
            'data' => [],
            'duplicateKey' => []
        ];
        foreach ($query->getQueryData() as $index => $tableData) {
            $data['tables'][$index] = $tableData[Query::QUERY_SET_TABLE];
            $data['columns'] = $tableData[Query::QUERY_SET_COLUMNS];
            self::buildValues($data['tables'][$index], $tableData[Query::QUERY_SET_DATA], $data['placeholders'], $data['data']);
            if (!empty($tableData[Query::QUERY_SET_DUPLICATE_KEY])) {
                self::buildDuplicateKeyUpdate($tableData[Query::QUERY_SET_DUPLICATE_KEY], $data['duplicateKey']);
            }
        }
        return $data;
    }

    /**
     * Helper function to build the on duplicate key update data values.
     * @param array $duplidateKeyData
     * @param array &$duplicateKey
     * @return void
     */
    public static function buildDuplicateKeyUpdate(array $duplicateKeyData, array &$duplicateKey)
    {
        foreach ($duplicateKeyData as $columnName => $duplicateValue) {
            // if it's an array then we're doing a complex operation and need to parse it
            // otherwise, we have two options:
            // update with the new value
            // or just pass through the value provided as-is
            // TODO: we probably need to wrap this in an escape sequence of sorts
            if (is_array($duplicateValue)) {
                $duplicateValue = self::buildDuplicateKeyComplexOperation($duplicateValue);
            } else if ($duplicateValue === Query::DUPLICATE_KEY_UPDATE) {
                $duplicateValue = "values(${columnName})";
            }
            $duplicateKey[] = "{$columnName} = {$duplicateValue}";
        }
    }

    /**
     * Helper function to parse and format a complex on duplicate key update condtion.
     * @param string $columnName
     * @param array $updateValues
     * @return string
     */
    public static function buildDuplicateKeyComplexOperation($columnName, array $updateValues)
    {
        $condition = [
            'c' => [
                Query::OPERATION_ADD => [
                    'a' => Query::DUPLICATE_KEY_UPDATE,
                    'b' => Query::DUPLICATE_KEY_UPDATE
                ]
            ]
        ];
        /**
         * 1. iterate the updateValues
         * 2. iterate the values array for the operation key
         * 3. if we encounter the value for the column is an array, that means we've encountered a nested operation
         *  3.1 in this case we need to send the column name (key) and the current value array
         * 4. if not an array, we check if it's a Query update condition or a hard coded value
         * 5. then we need to retain the current operation and the values that are acting on the operation (we probably need to push these through as a reference param too)
         * 6. once we have no more options, we need to iterate the array of ops => values and build the final string
         */
        // TODO: need to validate supplied operations are valid or throw an exception when we encounter an invalid one
        foreach ($updateValues as $operation => $items) {
            foreach ($items as $key => $updateValue) {
                if (is_array($updateValue)) {
                    // we need to recurse here

                } else {
                    if ($updateValue === Query::DUPLICATE_KEY_UPDATE) {

                    } else {

                    }
                }
            }
        }
    }

    /**
     * Helper function to build the placeholder/param conditions for an insert.
     * @param string $tableName
     * @param array $rowData
     * @param array &$placeholders
     * @param array &$data
     * @return void
     */
    public static function buildValues($tableName, array $rowData, array &$placeholders, array &$data)
    {
        // we need to get just the rows, if it's an array of arrays, just pull the first element
        // it's an array of arrays, so the first element is just the array of row data
        $rowData = is_array($rowData[0][0]) ? $rowData[0] : $rowData;
        // iterate each array of or row data
        foreach ($rowData as $rowIndex => $row) {
            // start each set of placeholder sets for the row
            $placeholders[$rowIndex] = $rowIndex === 0 ? '(' : ', (';
            // iterate each column value within the row
            foreach ($row as $itemCount => $item) {
                $placeholders[$rowIndex] .= $itemCount === 0 ? '?' : ', ?';
                $data[] = $item;
            }
            $placeholders[$rowIndex] .= ')';
        }
        $placeholders = implode('', $placeholders);
    }

    /**
     * Helper function to build join conditions.
     * @param string $tableName
     * @param array $joinConditions
     * @param array &$joins
     * @return void
     */
    public static function buildJoin($tableName, array $joinConditions, array &$joins)
    {
        $joinStmt = "{$joinConditions[2]} join {$joinConditions[0]} on";
        $join = '';
        foreach ($joinConditions[1] as $tableColumn => $joinTableColumn) {
            if (!StringHelper::isEmpty($join)) {
                $join .= " and";
            }
            $joinStmt .= " {$tableName}.{$tableColumn} = {$joinConditions[0]}.{$joinTableColumn}";
        }
        $joins[] = "{$joinStmt} {$join}";
    }

    /**
     * Helper function to build order by conditions.
     * @param string $tableName
     * @param array $orderConditions
     * @param array &$order
     * @return void
     */
    public static function buildOrder($tableName, array $orderConditions, array &$order)
    {
        foreach ($orderConditions as $currentOrder) {
            $order[] = "{$tableName}.{$currentOrder[0]} {$currentOrder[1]}";
        }
    }

    /**
     * Helper function to build set conditions and associated params.
     * @param string $tableName
     * @param array $setConditions
     * @param array &$sets
     * @param array &$params
     * @return void
     */
    public static function buildSet($tableName, array $setConditions, array &$sets, array &$params)
    {
        foreach ($setConditions as $currentSet) {
            $setVals = MySqlHelper::buildSetCondition($tableName, ...$currentSet);
            $sets[] = $setVals[0];
            $params[$setVals[1]] = $currentSet[1];
        }
    }

    /**
     * Helper function to build where conditions and associated params.
     * @param string $tableName
     * @param array $whereConditions
     * @param array &$where
     * @param array &$params
     * @return void
     */
    public static function buildWhere($tableName, array $whereConditions, array &$where, array &$params)
    {
        foreach ($whereConditions as $currentWhere) {
            $whereVals = MySqlHelper::buildWhereCondition($tableName, ...$currentWhere);
            $where[] = $whereVals[0];
            $params[$whereVals[1]] = $currentWhere[1];
        }
    }

    /**
     * Helper function to build a field line and return it in the appropriate format.
     * @param string $tableName
     * @param array $queryFields
     * @param array &$fields
     * @return void
     */
    public static function buildFields($tableName, array $queryFields, array &$fields)
    {
        if (count($queryFields) === 0) {
            $fields[] = "{$tableName}.*";
        } else {
            foreach ($queryFields as $field) {
                $fields[] = "{$tableName}.{$field}";
            }
        }
    }

    /**
     * Determines the parameter values for the field.
     * @param string $table
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    public static function determineParamName($table, $field, $value)
    {
        // if the string is already in the format :{string} it's already a placeholder, honor it
        $paramName = $value;
        if (!(is_string($value) && trim(substr($value, 0, 1)) === ':')) {
            $paramName = self::buildHashParam($table.$field);
        }
        return $paramName;
    }

    /**
     * Translates set parameters into appropriate set conditions.
     * @param string $table
     * @param string $columnName
     * @param mixed $value
     * @return array turle of [setString, paramName]
     */
    public static function buildSetCondition($table, $columnName, $value)
    {
        $paramName = self::determineParamName($table, $field, $value);
        return ["set {$table}.{$field} = {$paramName}", $paramName];
    }

    /**
     * Translates where parameters into appropriate where clause.
     * @param string $table
     * @param string $field
     * @param mixed $value
     * @param string $condition (optional)
     * @return array tuple of [whereString, paramName]
     */
    public static function buildWhereCondition($table, $field, $value, $condition = null)
    {
        $paramName = self::determineParamName($table, $field, $value);
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
