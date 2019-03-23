<?php
namespace InQuery\Exceptions;

use Inquery\Exceptions\BaseException;

/**
 * Exception for general database errors.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class DatabaseException extends BaseException
{
    /**
     * Define invalid params exception codes.
     */
    const FIND_EXCEPTION = 500;
}
