<?php
namespace InQuery\Commands;

use InQuery\Command;

/**
 * A built query that's ready for execution.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MockCommand implements Command
{
    /**
     * Command type.
     * @var string
     */
    protected $type = '';

    /**
     * Execution string.
     * @var string
     */
    protected $command = '';

    /**
     * Instantiate a new instance.
     * @param string $type
     * @param string $command
     */
    public function __construct($type, $command)
    {
        $this->type = $type;
        $this->command = $command;
    }

    /**
     * Returns command string.
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Returns command type.
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
