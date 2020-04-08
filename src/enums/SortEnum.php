<?php

namespace ParseCsv\enums;

class SortEnum extends AbstractEnum
{

    private const __DEFAULT = self::SORT_TYPE_REGULAR;

    public const SORT_TYPE_REGULAR = 'regular';
    public const SORT_TYPE_NUMERIC = 'numeric';
    public const SORT_TYPE_STRING = 'string';

    private static $sorting = [
        self::SORT_TYPE_REGULAR => SORT_REGULAR,
        self::SORT_TYPE_STRING => SORT_STRING,
        self::SORT_TYPE_NUMERIC => SORT_NUMERIC,
    ];

    /**
     * @param mixed $type
     *
     * @return mixed
     */
    public static function getSorting($type)
    {
        if (array_key_exists($type, self::$sorting)) {
            return self::$sorting[$type];
        }

        return self::$sorting[self::__DEFAULT];
    }
}
