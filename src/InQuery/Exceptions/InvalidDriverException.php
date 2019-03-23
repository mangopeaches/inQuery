<?php
namespace InQuery\Exceptions;

use Inquery\Exceptions\BaseException;

/**
 * Exception for when supplied driver was not found.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class InvalidDriverException extends BaseException
{
    /**
     * Define invalid params exception codes.
     */
    const INVALID_DRIVER = 300;
}
