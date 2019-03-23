<?php
namespace InQuery\Exceptions;

use Inquery\Exceptions\BaseException;

/**
 * Exception for when we cannot establish a connection to the database.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class DatabaseConnectionException extends BaseException
{
    /**
     * Define invalid params exception codes.
     */
    const CONNECT_ERROR = 400;
}
