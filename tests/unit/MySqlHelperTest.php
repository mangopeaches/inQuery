<?php
use PHPUnit\Framework\TestCase;

use InQuery\Query;
use InQuery\Helpers\MySqlHelper;
use InQuery\Helpers\StringHelper;

/**
 * Test cases for MySqlHelper class.
 * These need to be run in a separate process as the nature of InQuery is static props.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 * @runTestsInSeparateProcesses
 */
class MySqlHelperTest extends TestCase
{
    /**
     * Tests building a field hash param.
     */
    public function testBuildHashParam()
    {
        $table = 'tableName';
        $field = 'fieldName';
        $hashParam = MySqlHelper::buildHashParam($table.$field);
        $hash = ':' . StringHelper::hashString($table.$field);
        $this->assertTrue($hashParam === $hash);
    }

    /**
     * Data provider for base where conditions.
     * @return array
     */
    public function providerWhereConditions()
    {
        return [
            ['testTable', 'testField', 'bananas'],
            ['testTable', 'testField', ':bananas'],
            ['testTable', 'testField', 'bananas', Query::EQ],
            ['testTable', 'testField', ':bananas', Query::EQ],
            ['testTable', 'testField', 'bananas', Query::NOT_EQ],
            ['testTable', 'testField', ':bananas', Query::NOT_EQ],
            ['testTable', 'testField', 'bananas', Query::GT],
            ['testTable', 'testField', ':bananas', Query::GT],
            ['testTable', 'testField', 'bananas', Query::GT_EQ],
            ['testTable', 'testField', ':bananas', Query::GT_EQ],
            ['testTable', 'testField', 'bananas', Query::LT],
            ['testTable', 'testField', ':bananas', Query::LT],
            ['testTable', 'testField', 'bananas', Query::LT_EQ],
            ['testTable', 'testField', ':bananas', Query::LT_EQ]
        ];
    }

    /**
     * Tests building where condition for base conditions.
     * @param string $table
     * @param string $field
     * @param string $value
     * @param string $condition (optional)
     * @dataProvider providerWhereConditions
     */
    public function testBuildWhereBaseConditions($table, $field, $value, $condition = null)
    {
        $paramName = $value;
        if (!(is_string($value) && trim(substr($value, 0, 1)) === ':')) {
            $paramName = MySqlHelper::buildHashParam($table.$field);
        }
        $condition = $condition === null ? Query::EQ : $condition;
        $expected = ["{$table}.{$field} {$condition} {$paramName}", $paramName];
        $cond = MySqlHelper::buildWhere($table, $field, $value, $condition);
        $this->assertTrue($cond === $expected);
    }

    /**
     * Data provider for in query conditions.
     * @return array
     */
    public function providerInConditions()
    {
        return [
            ['testTable', 'testField', ['bananas', 'test'], Query::IN],
            ['testTable', 'testField', ':placeholder', Query::IN],
            ['testTable', 'testField', ['bananas', 'test'], Query::NOT_IN],
            ['testTable', 'testField', ':placeholder', Query::NOT_IN],
        ];
    }

    /**
     * Tests building in condition with value supplied.
     * @param string $table
     * @param string $field
     * @param string $value
     * @param string $condition (optional)
     * @dataProvider providerInConditions
     */
    public function testBuildWhereInConditionValue($table, $field, $value, $condition)
    {
        $paramName = $value;
        if (!(is_string($value) && trim(substr($value, 0, 1)) === ':')) {
            $paramName = MySqlHelper::buildHashParam($table.$field);
        }
        $expected = ["{$table}.{$field} {$condition} ({$paramName})", $paramName];
        $cond = MySqlHelper::buildWhere($table, $field, $value, $condition);
        $this->assertTrue($cond === $expected);
    }

    /**
     * Tests is null condition.
     */
    public function testBuildInNull()
    {
        $table = 'testTable';
        $field = 'testField';
        $value = '';
        if (!(is_string($value) && trim(substr($value, 0, 1)) === ':')) {
            $paramName = MySqlHelper::buildHashParam($table.$field);
        }
        $expected = ["{$table}.{$field} is null", $paramName];
        $cond = MySqlHelper::buildWhere($table, $field, $value, Query::IS_NULL);
        $this->assertTrue($cond === $expected);
    }
}
