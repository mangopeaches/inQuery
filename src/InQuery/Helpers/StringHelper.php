<?php
namespace InQuery\Helpers;

/**
 * General string helper functions.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class StringHelper
{
    /**
     * Generate a hash of a string.
     * @param string $string
     * @return string
     */
    public static function hashString($string)
    {
        return md5($string);
    }

    /**
     * Returns whether or not a string is the empty string.
     * @param string $string
     * @return bool
     */
    public static function isEmpty($string)
    {
        return trim($string) === '';
    }

    /**
     * Combines array of string with separator.
     * @param array $lines
     * @param string $glue
     * @return string
     */
    public static function joinLines(array $lines, $glue = ' ')
    {
        $lines = array_map('trim', $lines);
        return trim(implode($glue, $lines));
    }
}
