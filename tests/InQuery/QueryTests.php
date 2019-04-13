<?php
use PHPUnit\Framework\TestCase;

use InQuery\Query;
use InQuery\Drivers\MockDriver;
use InQuery\QueryBuilders\MockQueryBuilder;

/**
 * Test cases for Query class.
 * These need to be run in a separate process as the nature of InQuery is static props.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 * @runTestsInSeparateProcesses
 */
class QueryTests extends TestCase
{
    /**
     * Tests populating table.
     */
    public function testTable()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MockQueryBuilder());
        $query->table('test');
        $queryData = $query->getQueryData();
        $this->assertTrue($queryData[0][Query::QUERY_SET_TABLE] === 'test');
    }

    /**
     * Tests populating select fields.
     */
    public function testSelect()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MockQueryBuilder());
        $query->table('test')->select('column1', 'column2');
        $queryData = $query->getQueryData();
        $this->assertTrue($queryData[0][Query::QUERY_SET_FIELDS] === ['column1', 'column2']);
    }

    /**
     * Tests populating select fields multiple times.
     */
    public function testSelectMultiple()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MockQueryBuilder());
        $query->table('test')->select('column1', 'column2')->select('column2', 'column3');
        $queryData = $query->getQueryData();
        $this->assertTrue($queryData[0][Query::QUERY_SET_FIELDS] === ['column1', 'column2', 'column3']);
    }

    /**
     * Tests populating where.
     */
    public function testWhere()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MockQueryBuilder());
        $query->table('test')->select('column1', 'column2')->where('column', 'value');
        $queryData = $query->getQueryData();
        $this->assertTrue($queryData[0][Query::QUERY_SET_WHERE] === [['column', 'value', null]]);
    }

    /**
     * Tests populating multiple where conditions.
     */
    public function testWhereMultiple()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MockQueryBuilder());
        $query->table('test')->select('column1', 'column2')->where('column', 'value')->where('column2', ':test', '!=');
        $queryData = $query->getQueryData();
        $this->assertTrue($queryData[0][Query::QUERY_SET_WHERE] === [['column', 'value', null], ['column2', ':test', '!=']]);
    }

    /**
     * Tests populating order.
     */
    public function testOrder()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MockQueryBuilder());
        $query->table('test')->order('column1', Query::ORDER_ASC);
        $queryData = $query->getQueryData();
        $this->assertTrue($queryData[0][Query::QUERY_SET_ORDER] === [['column1', Query::ORDER_ASC]]);
    }

    /**
     * Tests populating multiple order conditions.
     */
    public function testOrderMultiple()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MockQueryBuilder());
        $query->table('test')->order('column1', Query::ORDER_ASC)->order('column2', Query::ORDER_DESC);
        $queryData = $query->getQueryData();
        $this->assertTrue($queryData[0][Query::QUERY_SET_ORDER] === [['column1', Query::ORDER_ASC], ['column2', Query::ORDER_DESC]]);
    }

    /**
     * Tests populating join.
     */
    public function testJoin()
    {
        $query = new Query(new MockDriver('localhost', 'test'), new MockQueryBuilder());
        $query->table('test')->join('joinTable', ['column1' => 'column1_fk']);
        $queryData = $query->getQueryData();
        $this->assertTrue($queryData[0][Query::QUERY_SET_JOIN] === ['joinTable', ['column1' => 'column1_fk'], Query::JOIN_TYPE_INNER]);
    }
}
