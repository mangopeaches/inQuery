<?php
use PHPUnit\Framework\TestCase;

use InQuery\InQuery;
use InQuery\Exceptions\SetupException;
use InQuery\Exceptions\InvalidParamsException;
use InQuery\Engine;
use InQuery\QueryResults\MockQueryResult;
use InQuery\Drivers\MockDriver;
use InQuery\Commands\MockCommand;
use InQuery\Command;
use InQuery\Query;

/**
 * Test cases for InQuery class.
 * These need to be run in a separate process as the nature of InQuery is static props.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 * @runTestsInSeparateProcesses
 */
class InQueryTest extends TestCase
{
    /**
     * Tests thrown error when calling getInstance() without init().
     */
    public function testSetupException()
    {
        $this->expectException(SetupException::class);
        InQuery::getInstance();
    }

    /**
     * Tests required params aren't present in config.
     */
    public function testInvalidParamsExceptionRequiredParams()
    {
        $this->expectException(InvalidParamsException::class);
        InQuery::init([
            'foo' => 'bar'
        ]);
    }

    /**
     * Tests driver param is required.
     */
    public function testInvalidParamsExceptionDriverRequired()
    {
        $this->expectException(InvalidParamsException::class);
        InQuery::init([
            'host' => 'asdf',
            'db' => 'sdsds'
        ]);
    }

    /**
     * Tests host param is required.
     */
    public function testInvalidParamsExceptionHostRequired()
    {
        $this->expectException(InvalidParamsException::class);
        InQuery::init([
            'engine' => 'asdf',
            'db' => 'sdsds'
        ]);
    }

    /**
     * Tests db param is required.
     */
    public function testInvalidParamsExceptionDBRequired()
    {
        $this->expectException(InvalidParamsException::class);
        InQuery::init([
            'engine' => 'asdf',
            'host' => 'sdsds'
        ]);
    }

    /**
     * Tests supplying empty array to init.
     */
    public function testInvalidParamsExceptionNoParams()
    {
        $this->expectException(InvalidParamsException::class);
        InQuery::init([]);
    }

    /**
     * Tests init with valid config params.
     */
    public function testValidParams()
    {
        $db = InQuery::init([
            'engine' => Engine::MOCK,
            'host' => 'localhost',
            'port' => '3306',
            'db' => 'mock',
            'username' => 'username',
            'password' => 'password'
        ]);
        $this->assertTrue($db instanceof InQuery);
    }

    /**
     * Tests getConnection() returns db connection.
     */
    public function testGetConnection()
    {
        $db = InQuery::init([
            'engine' => Engine::MOCK,
            'host' => 'localhost',
            'port' => '3306',
            'db' => 'mock',
            'username' => 'username',
            'password' => 'password'
        ]);
        $conn = $db::getInstance()->getConnection();
        $this->assertTrue($conn instanceof Engine);
    }

    /**
     * Tests creating with multiple param sets.
     */
    public function testValidMultipleParamSets()
    {
        $db = InQuery::init([
            [
                'engine' => Engine::MOCK,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'engine' => Engine::MOCK,
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertTrue($db instanceof InQuery);
    }

    /**
     * Tests accessing multiple db connection sets.
     */
    public function testValidMultipleParamSetsAccessNoName()
    {
        $db = InQuery::init([
            [
                'engine' => Engine::MOCK,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'engine' => Engine::MOCK,
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertTrue($db->{0} instanceof Engine);
        $this->assertTrue($db->{1} instanceof Engine);
    }

    /**
     * Tests accessing default driver without names.
     */
    public function testValidMultipleParamDefaultNoName()
    {
        $db = InQuery::init([
            [
                'engine' => Engine::MOCK,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'default' => true,
                'engine' => Engine::MOCK,
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        // assert that getting the default connection is the same as the one we have set as the default
        $this->assertEquals($db->getConnection(), $db->{1});
    }

    /**
     * Tests accessing multiple drivers for connection sets.
     */
    public function testValidMultipleParamSetsDriversNoName()
    {
        $db = InQuery::init([
            [
                'engine' => Engine::MOCK,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'engine' => Engine::MOCK,
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertTrue($db->{0} instanceof Engine);
        $this->assertTrue($db->{1} instanceof Engine);
    }

    /**
     * Tests creating with multiple param sets with name.
     */
    public function testValidMultipleParamSetsWithName()
    {
        $db = InQuery::init([
            [
                'engine' => Engine::MOCK,
                'name' => 'db1',
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'engine' => Engine::MOCK,
                'name' => 'db2',
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertTrue($db->db1 instanceof Engine);
        $this->assertTrue($db->db2 instanceof Engine);
    }

    /**
     * Tests accessing default driver with names.
     */
    public function testValidMultipleParamDefaultWithName()
    {
        $db = InQuery::init([
            [
                'name' => 'db1',
                'engine' => Engine::MOCK,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'default' => true,
                'name' => 'db2',
                'engine' => Engine::MOCK,
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertEquals($db->getConnection(), $db->db2);
    }

    /**
     * Tests basic usage for the query builder's find() method without params.
     */
    public function testQueryBuilderFindNoParams()
    {
        $db = InQuery::init([
            'engine' => Engine::MOCK,
            'name' => 'db1',
            'host' => 'localhost',
            'port' => '3306',
            'db' => 'mock',
            'username' => 'username',
            'password' => 'password'
        ])->getConnection();
        $findQuery = $db->query()->table('testTable');
        $this->assertTrue($findQuery instanceof Query);
    }

    /**
     * Tests basic usage for the execute method.
     */
    public function testDriverExecute()
    {
        $db = InQuery::init([
            'engine' => Engine::MOCK,
            'name' => 'db1',
            'host' => 'localhost',
            'port' => '3306',
            'db' => 'mock',
            'username' => 'username',
            'password' => 'password'
        ])->getConnection();
        $result = $db->query()->table('testTable')->get();
        $this->assertTrue($result instanceof MockQueryResult);
    }
}
