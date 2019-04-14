<?php
namespace InQuery\Helpers;

use InQuery\Helpers\StringHelper;
use InQuery\Query;

/**
 * MySql helper utilities.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MySqlHelper
{
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
            $paramName = StringHelper::hashString($table.$field);
        }
        $condition = $condition === null ? Query::EQ : $condition;
        switch ($condition) {
            case Query::IN:
                $whereString = "{$table}.{$field} IN (:${paramName})";
                break;
            case Query::NOT_IN:
                $whereString = "{$table}.{$field} NOT IN (:${paramName})";
                break;
            case Query::IS_NULL:
                $whereString = "{$table}.{$field} IS NULL";
                break;
            default:  
                $whereString = "{$table}.{$field} {$condition} :{$paramName}";
                break;
        }
        return [$whereString, $paramName];
    }
}
