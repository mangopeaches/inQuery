<?php
use PHPUnit\Framework\TestCase;

use InQuery\Query;
use InQuery\Drivers\MockDriver;
use InQuery\QueryBuilders\MySqlQueryBuilder;
use InQuery\Helpers\MySqlHelper;
use InQuery\Command;

/**
 * Test cases for MySqlQueryBuilder class.
 * These need to be run in a separate process as the nature of InQuery is static props.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 * @runTestsInSeparateProcesses
 */
class MySqlQueryBuilderTest extends TestCase
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
        $query->table('test')->select('column1', 'column2')
            ->order('column1', Query::ORDER_ASC)
            ->order('column2', Query::ORDER_DESC);
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
        $this->assertTrue($command->getCommand() === 'select test.*, test2.* from test inner join test2 on test.column1 = test2.column2');
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
        // MySqlQueryBuilder implicitly hashes {table}{field} together, that's why it's testcolumn1 for column1 of test table
        $paramHash = MySqlHelper::buildHashParam('testcolumn1');
        $command = $mysqlQueryBuilder->selectQuery($query);
        $this->assertTrue($command->getCommand() === "select test.column1, test.column2 from test where test.column1 = {$paramHash}");
        $this->assertTrue($command->getParams() === [$paramHash => $value]);
        $this->assertTrue($command->getType() === Command::TYPE_FIND);
    }

    /**
     * Tests where parameters are defined as placeholders.
     */
    public function testBoundParams()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MySqlQueryBuilder());
        $value = 'test';
        $query->table('test')->select('column1', 'column2')->where('column1', ':column1')->where('column2', ':column2');
        $mysqlQueryBuilder = new MySqlQueryBuilder();
        $command = $mysqlQueryBuilder->selectQuery($query);
        $this->assertTrue($command->getCommand() === "select test.column1, test.column2 from test where test.column1 = :column1 and test.column2 = :column2");
        $this->assertTrue($command->getParams() === [':column1' => ':column1', ':column2' => ':column2']);
        $this->assertTrue($command->getType() === Command::TYPE_FIND);
    }

    /**
     * Tests building a delete condition on a single table.
     */
    public function testDeleteSingleTable()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MySqlQueryBuilder());
        $query->table('test')->delete();
        $mysqlQueryBuilder = new MySqlQueryBuilder();
        $command = $mysqlQueryBuilder->deleteQuery($query);
        $this->assertTrue($command->getCommand() === "delete from test");
    }

    /**
     * Tests building a delete command across multiple tables.
     */
    public function testDeleteMultipleTables()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MySqlQueryBuilder());
        $query->table('test')->join('test2', ['test1Col' => 'test2Col'])->delete();
        $mysqlQueryBuilder = new MySqlQueryBuilder();
        $command = $mysqlQueryBuilder->deleteQuery($query);
        $this->assertTrue($command->getCommand() === "delete from test inner join test2 on test.test1Col = test2.test2Col");
    }
}
