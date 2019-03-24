<?php
namespace InQuery\Exceptions;

use InQuery\Exceptions\BaseException;

/**
 * Exception for when app is accessed before being initialized.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class SetupException extends BaseException
{
    /**
     * Define invalid params exception codes.
     */
    const GET_INSTANCE_ERROR = 100;
}
