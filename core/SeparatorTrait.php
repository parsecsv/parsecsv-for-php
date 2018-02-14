<?php

trait SeparatorTrait {
    /**
     * Detect separator using a nonstandard hack: such file starts with the
     * first line containing only "sep=;", where the last character is the
     * separator. Microsoft Excel is able to open such files.
     *
     * @param string $data    file data
     *
     * @return string|false detected delimiter, or false if none found
     */
    protected function _get_delimiter_from_sep_row($data) {
        $sep = false;
        // 32 bytes should be quite enough data for our sniffing, chosen arbitrarily
        $sepPrefix = substr($data, 0, 32);
        if (preg_match('/^sep=(.)\\r?\\n/i', $sepPrefix, $sepMatch)) {
            // we get separator.
            $sep = $sepMatch[1];
        }
        return $sep;
    }

    /**
     * Support for Excel-compatible sep=? row.
     *
     * @param string $data_string    file data to be updated
     *
     * @return bool TRUE if sep= line was found at the very beginning of the file
     */
    protected function _detect_and_remove_sep_row_from_data(&$data_string) {
        $sep = $this->_get_delimiter_from_sep_row($data_string);
        if ($sep === false) {
            return false;
        }

        $this->delimiter = $sep;

        // likely to be 5, but let's not assume we're always single-byte.
        $pos = 4 + strlen($sep);
        // the next characters should be a line-end
        if (substr($data_string, $pos, 1) === "\r") {
            $pos++;
        }
        if (substr($data_string, $pos, 1) === "\n") {
            $pos++;
        }

        // remove delimiter and its line-end (the data param is by-ref!)
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $data_string = substr($data_string, $pos);
        return true;
    }

    /**
     * @param int    $search_depth Number of rows to analyze
     * @param string $preferred    Preferred delimiter characters
     * @param string $enclosure    Enclosure character, default is double quote
     * @param string $data         The file content
     */
    protected function _guess_delimiter($search_depth, $preferred, $enclosure, &$data) {
        $chars = [];
        $strlen = strlen($data);
        $enclosed = false;
        $current_row = 1;
        $to_end = true;

        // The dash is the only character we don't want quoted, as it would
        // prevent character ranges within $auto_non_chars:
        $quoted_auto_non_chars = preg_quote($this->auto_non_chars, '/');
        $quoted_auto_non_chars = str_replace('\-', '-', $quoted_auto_non_chars);
        $pattern = '/[' . $quoted_auto_non_chars . ']/i';

        // walk specific depth finding possible delimiter characters
        for ($i = 0; $i < $strlen; $i++) {
            $ch = $data{$i};
            $nch = isset($data{$i + 1}) ? $data{$i + 1} : false;
            $pch = isset($data{$i - 1}) ? $data{$i - 1} : false;

            // open and closing quotes
            $is_newline = ($ch == "\n" && $pch != "\r") || $ch == "\r";
            if ($ch == $enclosure) {
                if (!$enclosed || $nch != $enclosure) {
                    $enclosed = $enclosed ? false : true;
                } elseif ($enclosed) {
                    $i++;
                }

                // end of row
            } elseif ($is_newline && !$enclosed) {
                if ($current_row >= $search_depth) {
                    $strlen = 0;
                    $to_end = false;
                } else {
                    $current_row++;
                }

                // count character
            } elseif (!$enclosed) {
                if (!preg_match($pattern, $ch)) {
                    if (!isset($chars[$ch][$current_row])) {
                        $chars[$ch][$current_row] = 1;
                    } else {
                        $chars[$ch][$current_row]++;
                    }
                }
            }
        }

        // filtering
        $depth = $to_end ? $current_row - 1 : $current_row;
        $filtered = [];
        foreach ($chars as $char => $value) {
            if ($match = $this->_check_count($char, $value, $depth, $preferred)) {
                $filtered[$match] = $char;
            }
        }

        // capture most probable delimiter
        ksort($filtered);
        $this->delimiter = reset($filtered);
    }

    /**
     * Check if passed info might be delimiter
     * Only used by find_delimiter
     *
     * @param  string $char      Potential field separating character
     * @param  array  $array     Frequency
     * @param  int    $depth     Number of analyzed rows
     * @param  string $preferred Preferred delimiter characters
     *
     * @return string|false      special string used for delimiter selection, or false
     */
    protected function _check_count($char, $array, $depth, $preferred) {
        if ($depth === count($array)) {
            $first = null;
            $equal = null;
            $almost = false;
            foreach ($array as $key => $value) {
                if ($first == null) {
                    $first = $value;
                } elseif ($value == $first && $equal !== false) {
                    $equal = true;
                } elseif ($value == $first + 1 && $equal !== false) {
                    $equal = true;
                    $almost = true;
                } else {
                    $equal = false;
                }
            }

            if ($equal) {
                $match = $almost ? 2 : 1;
                $pref = strpos($preferred, $char);
                $pref = ($pref !== false) ? str_pad($pref, 3, '0', STR_PAD_LEFT) : '999';

                return $pref . $match . '.' . (99999 - str_pad($first, 5, '0', STR_PAD_LEFT));
            } else {
                return false;
            }
        }
        return false;
    }
}
