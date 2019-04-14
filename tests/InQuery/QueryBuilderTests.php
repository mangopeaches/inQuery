<?php
use PHPUnit\Framework\TestCase;

use InQuery\Query;
use InQuery\Drivers\MockDriver;
use InQuery\QueryBuilders\MockQueryBuilder;
use InQuery\QueryBuilders\MySqlQueryBuilder;
use InQuery\Helpers\StringHelper;
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
     * Tests select all query against one table.
     */
    public function testSelectAll()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MySqlQueryBuilder());
        $query->table('test');
        $mysqlQueryBuilder = new MySqlQueryBuilder();
        $command = $mysqlQueryBuilder->selectQuery($query);
        $this->assertTrue($command->getCommand() === 'select test.* from test');
        $this->assertTrue($command->getParams() === []);
        $this->assertTrue($command->getType() === Command::TYPE_FIND);
    }

    /**
     * Tests selecting certain fields from a table.
     */
    public function testSelectFields()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MySqlQueryBuilder());
        $query->table('test')->select('column1', 'column2');
        $mysqlQueryBuilder = new MySqlQueryBuilder();
        $command = $mysqlQueryBuilder->selectQuery($query);
        $this->assertTrue($command->getCommand() === 'select test.column1, test.column2 from test');
        $this->assertTrue($command->getParams() === []);
        $this->assertTrue($command->getType() === Command::TYPE_FIND);
    }

    /**
     * Tests ordering by certain fields.
     */
    public function testOrderFields()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MySqlQueryBuilder());
        $query->table('test')->select('column1', 'column2')->order('column1', Query::ORDER_ASC)->order('column2', Query::ORDER_DESC);
        $mysqlQueryBuilder = new MySqlQueryBuilder();
        $command = $mysqlQueryBuilder->selectQuery($query);
        $this->assertTrue($command->getCommand() === 'select test.column1, test.column2 from test order by test.column1 asc, test.column2 desc');
        $this->assertTrue($command->getParams() === []);
        $this->assertTrue($command->getType() === Command::TYPE_FIND);
    }

    /**
     * Tests joining two tables.
     */
    public function testJoin()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MySqlQueryBuilder());
        $query->table('test')->join('test2', ['column1' => 'column2']);
        $mysqlQueryBuilder = new MySqlQueryBuilder();
        $command = $mysqlQueryBuilder->selectQuery($query);
        $this->assertTrue($command->getCommand() === 'select test.*, test2.* from test, test2 inner join test2 on test.column1 = test2.column2');
        $this->assertTrue($command->getParams() === []);
        $this->assertTrue($command->getType() === Command::TYPE_FIND);
    }

    /**
     * Tests where parameters are defined correctly.
     */
    public function testParams()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MySqlQueryBuilder());
        $value = 'test';
        $query->table('test')->select('column1', 'column2')->where('column1', $value);
        $mysqlQueryBuilder = new MySqlQueryBuilder();
        $paramHash = StringHelper::hashString('testcolumn1');
        $command = $mysqlQueryBuilder->selectQuery($query);
        $this->assertTrue($command->getCommand() === "select test.column1, test.column2 from test where test.column1 = :{$paramHash}");
        $this->assertTrue($command->getParams() === [$paramHash => $value]);
        $this->assertTrue($command->getType() === Command::TYPE_FIND);
    }
}
