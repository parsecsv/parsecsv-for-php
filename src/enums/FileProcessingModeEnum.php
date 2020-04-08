<?php

namespace ParseCsv\enums;

/**
 * Class FileProcessingEnum
 *
 * @package ParseCsv\enums
 *
 * todo extends a basic enum class after merging #121
 */
class FileProcessingModeEnum
{
    public const MODE_FILE_APPEND    = true;
    public const MODE_FILE_OVERWRITE = false;

    /**
     * @param bool $mode
     *
     * @return string
     */
    public static function getAppendMode(bool $mode): string
    {
        if ($mode === self::MODE_FILE_APPEND) {
            return 'ab';
        }

        return 'wb';
    }
}
