<?php
use PHPUnit\Framework\TestCase;

use InQuery\InQuery;
use InQuery\Engine;
use InQuery\QueryResults\MySqlQueryResult;

/**
 * Test cases for MySqlQueryResult classes.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MySqlQueryResultTest extends TestCase
{
    /**
     * Tests calling count on a select query result.
     */
    public function testFindCount()
    {
        $db = InQuery::init([
            'engine' => Engine::MYSQL,
            'name' => 'test',
            'host' => 'localhost',
            'port' => '3306',
            'db' => 'test',
            'username' => 'username',
            'password' => 'password'
        ])->getConnection();
        $result = $db->query()->table('test')->get();
        // TODO: insert some row
        // TODO: then remove dais rows and check affectedRows
        $this->assertTrue($result instanceof MySqlQueryResult);
        $this->assertTrue($result->count() === 1);
    }
}
