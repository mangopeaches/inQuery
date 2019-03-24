<?php
use PHPUnit\Framework\TestCase;

use InQuery\InQuery;
use InQuery\Exceptions\SetupException;
use InQuery\Exceptions\InvalidParamsException;
use InQuery\Drivers\MockDriver;
use InQuery\Connection;

/**
 * Test cases for InQuery class.
 * These need to be run in a separate process as the nature of InQuery is static props.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 * @runTestsInSeparateProcesses
 */
class InQueryTestCase extends TestCase
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
            'driver' => 'asdf',
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
            'driver' => 'asdf',
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
            'driver' => MockDriver::NAME,
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
            'driver' => MockDriver::NAME,
            'host' => 'localhost',
            'port' => '3306',
            'db' => 'mock',
            'username' => 'username',
            'password' => 'password'
        ]);
        $conn = $db::getInstance()->getConnection();
        $this->assertTrue($conn instanceof Connection);
        $this->assertTrue($conn->getName() === 0);
    }

    /**
     * Tests getConnection() with name.
     */
    public function testGetConnectionWithName()
    {
        $db = InQuery::init([
            'driver' => MockDriver::NAME,
            'name' => 'testConn',
            'host' => 'localhost',
            'port' => '3306',
            'db' => 'mock',
            'username' => 'username',
            'password' => 'password'
        ]);
        $conn = $db::getInstance()->getConnection();
        $this->assertTrue($conn instanceof Connection);
        $this->assertTrue($conn->getName() === 'testConn');
    }

    /**
     * Tests creating with multiple param sets.
     */
    public function testValidMultipleParamSets()
    {
        $db = InQuery::init([
            [
                'driver' => MockDriver::NAME,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'driver' => MockDriver::NAME,
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
                'driver' => MockDriver::NAME,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'driver' => MockDriver::NAME,
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertTrue($db->{0} instanceof Connection);
        $this->assertTrue($db->{1} instanceof Connection);
        $this->assertTrue($db->{0}->getName() === 0);
        $this->assertTrue($db->{1}->getName() === 1);
    }

    /**
     * Tests accessing default driver without names.
     */
    public function testValidMultipleParamDefaultNoName()
    {
        $db = InQuery::init([
            [
                'driver' => MockDriver::NAME,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'default' => true,
                'driver' => MockDriver::NAME,
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        // it's by original index, so index 1 gets default position
        $this->assertTrue($db->getConnection()->getName() === 1);
    }

    /**
     * Tests accessing multiple drivers for connection sets.
     */
    public function testValidMultipleParamSetsDriversNoName()
    {
        $db = InQuery::init([
            [
                'driver' => MockDriver::NAME,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'driver' => MockDriver::NAME,
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertTrue($db->{0} instanceof Connection);
        $this->assertTrue($db->{1} instanceof Connection);
        $this->assertTrue($db->{0}->getDriver() instanceof MockDriver);
        $this->assertTrue($db->{1}->getDriver() instanceof MockDriver);
    }

    /**
     * Tests creating with multiple param sets with name.
     */
    public function testValidMultipleParamSetsWithName()
    {
        $db = InQuery::init([
            [
                'driver' => MockDriver::NAME,
                'name' => 'db1',
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'driver' => MockDriver::NAME,
                'name' => 'db2',
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertTrue($db->db1 instanceof Connection);
        $this->assertTrue($db->db2 instanceof Connection);
        $this->assertTrue($db->db1->getName() === 'db1');
        $this->assertTrue($db->db2->getName() === 'db2');
    }

    /**
     * Tests accessing default driver with names.
     */
    public function testValidMultipleParamDefaultWithName()
    {
        $db = InQuery::init([
            [
                'name' => 'db1',
                'driver' => MockDriver::NAME,
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'default' => true,
                'name' => 'db2',
                'driver' => MockDriver::NAME,
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertTrue($db->getConnection()->getName() === 'db2');
    }

    /**
     * Tests get each connection driver by name.
     */
    public function testValidMultipleParamSetsDriversWithName()
    {
        $db = InQuery::init([
            [
                'driver' => MockDriver::NAME,
                'name' => 'db1',
                'host' => 'localhost',
                'port' => '3306',
                'db' => 'mock',
                'username' => 'username',
                'password' => 'password'
            ],
            [
                'driver' => MockDriver::NAME,
                'name' => 'db2',
                'host' => 'localhost2',
                'port' => '3306',
                'db' => 'mock2',
                'username' => 'username',
                'password' => 'password'
            ]
        ]);
        $this->assertTrue($db->db1->getDriver() instanceof MockDriver);
        $this->assertTrue($db->db2->getDriver() instanceof MockDriver);
    }
}
