<?php
namespace InQuery;

/**
 * Interface for a built query which is ready for execution.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
interface Command
{
    /**
     * Type constants.
     */
    const TYPE_FIND = 'find';
    const TYPE_INSERT = 'insert';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';

    /**
     * Returns command value.
     * @return string|array
     */
    public function getCommand();

    /**
     * Returns params.
     * @return array
     */
    public function getParams();

    /**
     * Returns command type.
     * @return string
     */
    public function getType();
}
