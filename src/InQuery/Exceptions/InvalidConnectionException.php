<?php
namespace InQuery\Exceptions;

use Inquery\Exceptions\BaseException;

/**
 * Exception for when invalid connection name is requested.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class InvalidConnectionException extends BaseException
{
    /**
     * Define invalid params exception codes.
     */
    const INVALID_CONNECTION = 200;
}
