<?php
use PHPUnit\Framework\TestCase;

use InQuery\Commands\MockCommand;
use InQuery\Command;

/**
 * Test cases for Command class.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class CommandTest extends TestCase
{
    /**
     * Tests thrown error when calling getInstance() without init().
     */
    public function testEmptyFindCommand()
    {
        $type = Command::TYPE_FIND;
        $commandString = "SELECT bananas FROM monkeys WHERE bananas = :bananas";
        $params = [':bananas' => 'delicious'];
        $command = new MockCommand($type, $commandString, $params);
        $this->assertEquals($command->getType(), $type);
        $this->assertEquals($command->getCommand(), $commandString);
        $this->assertEquals($command->getParams(), $params);
    }

}
