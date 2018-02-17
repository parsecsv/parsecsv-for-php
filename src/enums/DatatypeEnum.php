<?php
namespace CSV\enums;

/**
 * Class DatatypeEnum
 *
 * @package CSV\enums
 */
class DatatypeEnum extends SplEnum {

    const __DEFAULT = self::TYPE_STRING;

    const TYPE_STRING = 'string';

    const TYPE_FLOAT = 'float';

    const TYPE_INT = 'integer';

    const TYPE_BOOL = 'boolean';

    const TYPE_DATE = 'date';

    const REGEX_FLOAT = '/^[+-]?([0-9]*[.,])?([0-9]|[.,][0-9])+$/';

    const REGEX_INT = '/^[-+]?[0-9]\d*$/';

    const REGEX_BOOL = '/^(?i:true|false)$/';

    /**
     * Define validator functions here.
     *
     * @var array
     */
    private static $validators = array(
        self::TYPE_STRING => null,
        self::TYPE_FLOAT => 'isValidFloat',
        self::TYPE_INT => 'isValidInteger',
        self::TYPE_BOOL => 'isValidBoolean',
        self::TYPE_DATE => 'isValidDate'
    );

    /**
     * Checks data type for given string.
     *
     * @param $value
     *
     * @return bool|string
     */
    public static function getValidTypeFromSample($value){
        $value = trim((string) $value);

        if (empty($value)){
            return false;
        }

        foreach (self::$validators as $type => $validator){
            if ($validator === null){
                continue;
            }

            if (method_exists(self, $validator)){
                call_user_func($validator($value));
                return $type;
            }

            return self::__DEFAULT;
        }
    }

    /**
     * Check if string is float value.
     *
     * @param $value
     *
     * @return false|int
     */
    private static function isValidFloat($value) {
        return preg_match(self::REGEX_FLOAT, $value);
    }

    /**
     * Check if string is integer value.
     *
     * @param $value
     *
     * @return false|int
     */
    private static function isValidInteger($value) {
        return preg_match(self::REGEX_INT, $value);
    }

    /**
     * Check if string is boolean.
     *
     * @param $value
     *
     * @return false|int
     */
    private static function isValidBoolean($value) {
        return preg_match(self::REGEX_BOOL, $value);
    }

    /**
     * Check if string is date.
     *
     * @param $value
     *
     * @return false|int
     */
    private static function isValidDate($value) {
        return (bool) strtotime($value);
    }
}
