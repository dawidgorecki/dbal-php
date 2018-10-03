<?php

namespace Reven\DBAL\Utils;

/**
 * Class StringUtils
 * @package Reven\DBAL\Utils
 */
class StringUtils
{

    /**
     * Convert StudlyCaps or camelCase string to array
     *
     * @param string $string
     * @return array
     */
    public static function splitByCapitalLetter(string $string): array
    {
        return preg_split('/(?=[A-Z])/', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Convert StudlyCaps or camelCase string to the string with underscores separating words
     *
     * @param string $string
     * @return string
     */
    public static function convertToUnderscored(string $string): string
    {
        return strtolower(implode('_', self::splitByCapitalLetter($string)));
    }

    /**
     * Convert the string with specified words separator to StudlyCaps
     *
     * @param string $string
     * @param string $separator
     * @return string
     */
    public static function convertToStudlyCaps(string $string, string $separator = '_'): string
    {
        return str_replace(' ', '', ucwords(str_replace($separator, ' ', strtolower($string))));
    }

    /**
     * Convert the string with specified words separator to camelCase
     *
     * @param string $string
     * @param string $separator
     * @return string
     */
    public static function convertToCamelCase(string $string, string $separator = '_'): string
    {
        return lcfirst(self::convertToStudlyCaps($string, $separator));
    }

}
