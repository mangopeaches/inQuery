<?php
use PHPUnit\Framework\TestCase;

use InQuery\Query;
use InQuery\Drivers\MockDriver;
use InQuery\QueryBuilders\MockQueryBuilder;
use InQuery\QueryBuilders\MySqlQueryBuilder;
use InQuery\Command;

/**
 * Test cases for QueryBuilder classes.
 * These need to be run in a separate process as the nature of InQuery is static props.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 * @runTestsInSeparateProcesses
 */
class QueryBuilderTests extends TestCase
{
    /**
     * Tests populating table.
     */
    public function testTable()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MockQueryBuilder());
        $query->table('test');
        $mysqlQueryBuilder = new MySqlQueryBuilder();
        $command = $mysqlQueryBuilder->selectQuery($query);
        $this->assertTrue($command->getCommand() === 'select * from test');
        $this->assertTrue($command->getParams() === []);
        $this->assertTrue($command->getType() === Command::TYPE_FIND);
    }
}
