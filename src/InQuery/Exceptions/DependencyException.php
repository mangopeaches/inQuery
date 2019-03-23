<?php
namespace InQuery\Exceptions;

use Inquery\Exceptions\BaseException;

/**
 * Exception for missing dependencies.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class DependencyException extends BaseException
{
    /**
     * Define invalid params exception codes.
     */
    const MISSING_MONGO_DRIVER = 600;
}
