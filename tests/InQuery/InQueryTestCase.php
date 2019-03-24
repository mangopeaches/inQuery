<?php
use PHPUnit\Framework\TestCase;

use InQuery\InQuery;
use InQuery\Exceptions\SetupException;

/**
 * Test cases for InQuery class.
 * @author Thomas Breese <thomasjbreese@gmail.com>
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
}
