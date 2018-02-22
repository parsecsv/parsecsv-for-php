<?php
namespace ParseCsv\enums;


class SortEnum extends AbstractEnum {
    const __DEFAULT = self::SORT_TYPE_REGULAR;

    const SORT_TYPE_REGULAR = SORT_REGULAR;

    const SORT_TYPE_NUMERIC = SORT_NUMERIC;

    const SORT_TYPE_STRING = SORT_STRING;
}
