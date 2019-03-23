<?php
namespace InQuery\Exceptions;

use Inquery\Exceptions\BaseException;

/**
 * Exception for when invalid parameters are supplied.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class InvalidParamsException extends BaseException
{
    /**
     * Define invalid params exception codes.
     */
    const INIT_PARAMS_INVALID = 1;
}
