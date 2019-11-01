<?php
namespace InQuery\Commands;

use InQuery\Command;

/**
 * A built query that's ready for execution.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MySqlCommand implements Command
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
     * Query params.
     * @var array
     */
    protected $params = [];

    /**
     * Instantiate a new instance.
     * @param string $type
     * @param string $command
     * @param array $params
     */
    public function __construct($type, $command, array $params = [])
    {
        $this->type = $type;
        $this->command = $command;
        $this->params = $params;
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
     * Returns params.
     * @return array
     */
    public function getParams()
    {
        return $this->params;
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
