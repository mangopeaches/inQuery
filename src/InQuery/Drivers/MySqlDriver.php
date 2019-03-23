<?php
namespace InQuery\Drivers;

use InQuery\Driver;
use InQuery\Drivers\BaseDriver;

/**
 * MySql database driver.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class MySqlDriver extends BaseDriver implements Driver
{
    /**
     * Define constants.
     */
    const NAME = 'mysql';
}
