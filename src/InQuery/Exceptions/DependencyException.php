<?php
namespace InQuery\Exceptions;

use InQuery\Exceptions\BaseException;

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
    const MISSING_PDO_DRIVER = 700;
}
