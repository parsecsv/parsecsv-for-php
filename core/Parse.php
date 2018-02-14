<?php

trait Parse {
    /**
     * Parse File
     * Read file to string and call parse_string()
     *
     * @param  string|null $file Local CSV file
     *
     * @return array|bool
     */
    protected function parse_file($file = null) {
        if (is_null($file)) {
            $file = $this->file;
        }

        if (empty($this->file_data)) {
            $this->load_data($file);
        }

        return !empty($this->file_data) ? $this->parse_string() : false;
    }

    /**
     * Parse CSV strings to arrays. If you need BOM detection or character
     * encoding conversion, please call load_data() first, followed by a call to
     * parse_string() with no parameters.
     *
     * To detect field separators, please use auto() instead.
     *
     * @param string $data CSV data
     *
     * @return array|false - 2D array with CSV data, or false on failure
     */
    protected function parse_string($data = null) {
        if (empty($data)) {
            if ($this->_check_data()) {
                $data = &$this->file_data;
            } else {
                return false;
            }
        }

        $white_spaces = str_replace($this->delimiter, '', " \t\x0B\0");

        $rows = array();
        $row = array();
        $row_count = 0;
        $current = '';
        $head = !empty($this->fields) ? $this->fields : array();
        $col = 0;
        $enclosed = false;
        $was_enclosed = false;
        $strlen = strlen($data);

        // force the parser to process end of data as a character (false) when
        // data does not end with a line feed or carriage return character.
        $lch = $data{$strlen - 1};
        if ($lch != "\n" && $lch != "\r") {
            $data .= "\n";
            $strlen++;
        }

        // walk through each character
        for ($i = 0; $i < $strlen; $i++) {
            $ch = isset($data{$i}) ? $data{$i} : false;
            $nch = isset($data{$i + 1}) ? $data{$i + 1} : false;

            // open/close quotes, and inline quotes
            if ($ch == $this->enclosure) {
                if (!$enclosed) {
                    if (ltrim($current, $white_spaces) == '') {
                        $enclosed = true;
                        $was_enclosed = true;
                    } else {
                        $this->error = 2;
                        $error_row = count($rows) + 1;
                        $error_col = $col + 1;
                        $index = $error_row . '-' . $error_col;
                        if (!isset($this->error_info[$index])) {
                            $this->error_info[$index] = array(
                                'type' => 2,
                                'info' => 'Syntax error found on row ' . $error_row . '. Non-enclosed fields can not contain double-quotes.',
                                'row' => $error_row,
                                'field' => $error_col,
                                'field_name' => !empty($head[$col]) ? $head[$col] : null,
                            );
                        }

                        $current .= $ch;
                    }
                } elseif ($nch == $this->enclosure) {
                    $current .= $ch;
                    $i++;
                } elseif ($nch != $this->delimiter && $nch != "\r" && $nch != "\n") {
                    $x = $i + 1;
                    while (isset($data{$x}) && ltrim($data{$x}, $white_spaces) == '') {
                        $x++;
                    }
                    if ($data{$x} == $this->delimiter) {
                        $enclosed = false;
                        $i = $x;
                    } else {
                        if ($this->error < 1) {
                            $this->error = 1;
                        }

                        $error_row = count($rows) + 1;
                        $error_col = $col + 1;
                        $index = $error_row . '-' . $error_col;
                        if (!isset($this->error_info[$index])) {
                            $this->error_info[$index] = array(
                                'type' => 1,
                                'info' =>
                                    'Syntax error found on row ' . (count($rows) + 1) . '. ' .
                                    'A single double-quote was found within an enclosed string. ' .
                                    'Enclosed double-quotes must be escaped with a second double-quote.',
                                'row' => count($rows) + 1,
                                'field' => $col + 1,
                                'field_name' => !empty($head[$col]) ? $head[$col] : null,
                            );
                        }

                        $current .= $ch;
                        $enclosed = false;
                    }
                } else {
                    $enclosed = false;
                }

                // end of field/row/csv
            } elseif (($ch === $this->delimiter || $ch == "\n" || $ch == "\r" || $ch === false) && !$enclosed) {
                $key = !empty($head[$col]) ? $head[$col] : $col;
                $row[$key] = $was_enclosed ? $current : trim($current);
                $current = '';
                $was_enclosed = false;
                $col++;

                // end of row
                if ($ch == "\n" || $ch == "\r" || $ch === false) {
                    if ($this->_validate_offset($row_count) && $this->_validate_row_conditions($row, $this->conditions)) {
                        if ($this->heading && empty($head)) {
                            $head = $row;
                        } elseif (empty($this->fields) || (!empty($this->fields) && (($this->heading && $row_count > 0) || !$this->heading))) {
                            if (!empty($this->sort_by) && !empty($row[$this->sort_by])) {
                                $sort_field = $row[$this->sort_by];
                                if (isset($rows[$sort_field])) {
                                    $rows[$sort_field . '_0'] = &$rows[$sort_field];
                                    unset($rows[$sort_field]);
                                    $sn = 1;
                                    while (isset($rows[$sort_field . '_' . $sn])) {
                                        $sn++;
                                    }
                                    $rows[$sort_field . '_' . $sn] = $row;
                                } else {
                                    $rows[$sort_field] = $row;
                                }

                            } else {
                                $rows[] = $row;
                            }
                        }
                    }

                    $row = array();
                    $col = 0;
                    $row_count++;

                    if ($this->sort_by === null && $this->limit !== null && count($rows) == $this->limit) {
                        $i = $strlen;
                    }

                    if ($ch == "\r" && $nch == "\n") {
                        $i++;
                    }
                }

                // append character to current field
            } else {
                $current .= $ch;
            }
        }

        $this->titles = $head;
        if (!empty($this->sort_by)) {
            $sort_type = SORT_REGULAR;
            if ($this->sort_type == 'numeric') {
                $sort_type = SORT_NUMERIC;
            } elseif ($this->sort_type == 'string') {
                $sort_type = SORT_STRING;
            }

            $this->sort_reverse ? krsort($rows, $sort_type) : ksort($rows, $sort_type);

            if ($this->offset !== null || $this->limit !== null) {
                $rows = array_slice($rows, ($this->offset === null ? 0 : $this->offset), $this->limit, true);
            }
        }

        if (!$this->keep_file_data) {
            $this->file_data = null;
        }

        return $rows;
    }

    /**
     * Load local file or string
     *
     * @param string|null $input local CSV file
     *
     * @return  true or false
     */
    protected function load_data($input = null) {
        $data = null;
        $file = null;

        if (is_null($input)) {
            $file = $this->file;
        } elseif (file_exists($input)) {
            $file = $input;
        } else {
            $data = $input;
        }

        if (!empty($data) || $data = $this->_rfile($file)) {
            if ($this->file != $file) {
                $this->file = $file;
            }

            if (preg_match('/\.php$/i', $file) && preg_match('/<\?.*?\?>(.*)/ms', $data, $strip)) {
                $data = ltrim($strip[1]);
            }

            if (strpos($data, "\xef\xbb\xbf") === 0) {
                // strip off BOM (UTF-8)
                $data = substr($data, 3);
                $this->encoding('UTF-8');
            } elseif (strpos($data, "\xff\xfe") === 0) {
                // strip off BOM (UTF-16 little endian)
                $data = substr($data, 2);
                $this->encoding("UCS-2LE");
            } elseif (strpos($data, "\xfe\xff") === 0) {
                // strip off BOM (UTF-16 big endian)
                $data = substr($data, 2);
                $this->encoding("UTF-16");
            }

            if ($this->convert_encoding && $this->input_encoding !== $this->output_encoding) {
                $data = $this->use_mb_convert_encoding ?
                    mb_convert_encoding($data, $this->output_encoding, $this->input_encoding) :
                    iconv($this->input_encoding, $this->output_encoding, $data);
            }

            if (substr($data, -1) != "\n") {
                $data .= "\n";
            }

            $this->file_data = &$data;
            return true;
        }

        return false;
    }

    /**
     * Validate a row against specified conditions
     *
     * @param array       $row        array with values from a row
     * @param string|null $conditions specified conditions that the row must match
     *
     * @return  true of false
     */
    protected function _validate_row_conditions($row = array(), $conditions = null) {
        if (!empty($row)) {
            if (!empty($conditions)) {
                $condition_array = (strpos($conditions, ' OR ') !== false) ?
                    explode(' OR ', $conditions) :
                    array($conditions);
                $or = '';
                foreach ($condition_array as $key => $value) {
                    if (strpos($value, ' AND ') !== false) {
                        $value = explode(' AND ', $value);
                        $and = '';

                        foreach ($value as $k => $v) {
                            $and .= $this->_validate_row_condition($row, $v);
                        }

                        $or .= (strpos($and, '0') !== false) ? '0' : '1';
                    } else {
                        $or .= $this->_validate_row_condition($row, $value);
                    }
                }

                return strpos($or, '1') !== false;
            }

            return true;
        }

        return false;
    }

    /**
     * Validate a row against a single condition
     *
     * @param array  $row       array with values from a row
     * @param string $condition specified condition that the row must match
     *
     * @return string single 0 or 1
     */
    protected function _validate_row_condition($row, $condition) {
        $operators = array(
            '=', 'equals', 'is',
            '!=', 'is not',
            '<', 'is less than',
            '>', 'is greater than',
            '<=', 'is less than or equals',
            '>=', 'is greater than or equals',
            'contains',
            'does not contain',
        );

        $operators_regex = array();

        foreach ($operators as $value) {
            $operators_regex[] = preg_quote($value, '/');
        }

        $operators_regex = implode('|', $operators_regex);

        if (preg_match('/^(.+) (' . $operators_regex . ') (.+)$/i', trim($condition), $capture)) {
            $field = $capture[1];
            $op = $capture[2];
            $value = $capture[3];

            if (preg_match('/^([\'\"]{1})(.*)([\'\"]{1})$/', $value, $capture)) {
                if ($capture[1] == $capture[3]) {
                    $value = strtr($capture[2], array(
                        "\\n" => "\n",
                        "\\r" => "\r",
                        "\\t" => "\t",
                    ));

                    $value = stripslashes($value);
                }
            }

            if (array_key_exists($field, $row)) {
                if (($op == '=' || $op == 'equals' || $op == 'is') && $row[$field] == $value) {
                    return '1';
                } elseif (($op == '!=' || $op == 'is not') && $row[$field] != $value) {
                    return '1';
                } elseif (($op == '<' || $op == 'is less than') && $row[$field] < $value) {
                    return '1';
                } elseif (($op == '>' || $op == 'is greater than') && $row[$field] > $value) {
                    return '1';
                } elseif (($op == '<=' || $op == 'is less than or equals') && $row[$field] <= $value) {
                    return '1';
                } elseif (($op == '>=' || $op == 'is greater than or equals') && $row[$field] >= $value) {
                    return '1';
                } elseif ($op == 'contains' && preg_match('/' . preg_quote($value, '/') . '/i', $row[$field])) {
                    return '1';
                } elseif ($op == 'does not contain' && !preg_match('/' . preg_quote($value, '/') . '/i', $row[$field])) {
                    return '1';
                } else {
                    return '0';
                }
            }
        }

        return '1';
    }

    /**
     * Validates if the row is within the offset or not if sorting is disabled
     *
     * @param int $current_row the current row number being processed
     *
     * @return  true of false
     */
    protected function _validate_offset($current_row) {
        return
            $this->sort_by !== null ||
            $this->offset === null ||
            $current_row >= $this->offset;
    }

    /**
     * Check file data
     *
     * @param  string|null $file local filename
     *
     * @return bool
     */
    protected function _check_data($file = null) {
        if (empty($this->file_data)) {
            if (is_null($file)) {
                $file = $this->file;
            }

            return $this->load_data($file);
        }

        return true;
    }

    /**
     * Enclose values if needed
     *  - only used by unparse()
     *
     * @param string $value     Cell value to process
     * @param string $delimiter Character to put between cells on the same row
     *
     * @return string Processed value
     */
    protected function _enclose_value($value = null, $delimiter) {
        if ($value !== null && $value != '') {
            $delimiter_quoted = $delimiter ?
                preg_quote($delimiter, '/') . "|"
                : '';
            $enclosure_quoted = preg_quote($this->enclosure, '/');
            $pattern = "/" . $delimiter_quoted . $enclosure_quoted . "|\n|\r/i";
            if ($this->enclose_all || preg_match($pattern, $value) || ($value{0} == ' ' || substr($value, -1) == ' ')) {
                $value = str_replace($this->enclosure, $this->enclosure . $this->enclosure, $value);
                $value = $this->enclosure . $value . $this->enclosure;
            }
        }

        return $value;
    }

    /**
     * Read local file
     *
     * @param string|null $file local filename
     *
     * @return string|false Data from file, or false on failure
     */
    protected function _rfile($file = null) {
        if (!is_readable($file)) {
            return false;
        }

        if (!($fh = fopen($file, 'r'))) {
            return false;
        }

        $data = fread($fh, filesize($file));
        fclose($fh);

        return $data;
    }
}
