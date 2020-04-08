<?php

namespace ParseCsv\enums;

/**
 * Class DatatypeEnum
 *
 * @package ParseCsv\enums
 *
 * todo: needs a basic parent enum class for error handling.
 */
class DatatypeEnum extends AbstractEnum
{
    private const __DEFAULT = self::TYPE_STRING;

    public const TYPE_STRING = 'string';

    public const TYPE_FLOAT = 'float';

    public const TYPE_INT = 'integer';

    public const TYPE_BOOL = 'boolean';

    public const TYPE_DATE = 'date';

    private const REGEX_FLOAT = '/(^[+-]?$)|(^[+-]?[0-9]+([,.][0-9])?[0-9]*(e[+-]?[0-9]+)?$)/';

    private const REGEX_INT = '/^[-+]?\d\d*$/';

    private const REGEX_BOOL = '/^(?i:true|false)$/';

    /**
     * Define validator functions here.
     *
     * @var array
     *
     * @uses isValidFloat
     * @uses isValidInteger
     * @uses isValidBoolean
     * @uses isValidDate
     */
    private static $validators = [
        self::TYPE_STRING => null,
        self::TYPE_INT => 'isValidInteger',
        self::TYPE_BOOL => 'isValidBoolean',
        self::TYPE_FLOAT => 'isValidFloat',
        self::TYPE_DATE => 'isValidDate',
    ];

    /**
     * Checks data type for given string.
     *
     * @param string $value
     *
     * @return bool|string
     */
    public static function getValidTypeFromSample(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            return false;
        }

        foreach (self::$validators as $type => $validator) {
            if ($validator === null) {
                continue;
            }

            if (method_exists(__CLASS__, $validator) && self::$validator($value)) {
                return $type;
            }
        }

        return self::__DEFAULT;
    }

    /**
     * Check if string is float value.
     *
     * @param string $value
     *
     * @return bool
     */
    private static function isValidFloat(string $value): bool
    {
        return (bool) preg_match(self::REGEX_FLOAT, $value);
    }

    /**
     * Check if string is integer value.
     *
     * @param string $value
     *
     * @return bool
     */
    private static function isValidInteger(string $value): bool
    {
        return (bool) preg_match(self::REGEX_INT, $value);
    }

    /**
     * Check if string is boolean.
     *
     * @param string $value
     *
     * @return bool
     */
    private static function isValidBoolean(string $value): bool
    {
        return (bool) preg_match(self::REGEX_BOOL, $value);
    }

    /**
     * Check if string is date.
     *
     * @param string $value
     *
     * @return bool
     */
    private static function isValidDate(string $value): bool
    {
        return (bool) strtotime($value);
    }
}
