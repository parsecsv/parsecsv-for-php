<?php

class parseCSV {

    /*
    Class: parseCSV v0.4.3 beta
    https://github.com/parsecsv/parsecsv-for-php

    Fully conforms to the specifications lined out on Wikipedia:
    - http://en.wikipedia.org/wiki/Comma-separated_values

    Based on the concept of Ming Hong Ng's CsvFileParser class:
    - http://minghong.blogspot.com/2006/07/csv-parser-for-php.html


    (The MIT license)

    Copyright (c) 2014 Jim Myhrberg.

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.


    Code Examples
    ----------------
    # general usage
    $csv = new parseCSV('data.csv');
    print_r($csv->data);
    ----------------
    # tab delimited, and encoding conversion
    $csv = new parseCSV();
    $csv->encoding('UTF-16', 'UTF-8');
    $csv->delimiter = "\t";
    $csv->parse('data.tsv');
    print_r($csv->data);
    ----------------
    # auto-detect delimiter character
    $csv = new parseCSV();
    $csv->auto('data.csv');
    print_r($csv->data);
    ----------------
    # modify data in a csv file
    $csv = new parseCSV();
    $csv->sort_by = 'id';
    $csv->parse('data.csv');
    # "4" is the value of the "id" column of the CSV row
    $csv->data[4] = array('firstname' => 'John', 'lastname' => 'Doe', 'email' => 'john@doe.com');
    $csv->save();
    ----------------
    # add row/entry to end of CSV file
    #  - only recommended when you know the exact structure of the file
    $csv = new parseCSV();
    $csv->save('data.csv', array(array('1986', 'Home', 'Nowhere', '')), true);
    ----------------
    # convert 2D array to csv data and send headers
    # to browser to treat output as a file and download it
    $csv = new parseCSV();
    $csv->output('movies.csv', $array, array('field 1', 'field 2'), ',');
    ----------------
     */

    /**
     * Configuration
     * - set these options with $object->var_name = 'value';
     */

    /**
     * Heading
     * Use first line/entry as field names
     *
     * @var bool
     */
    public $heading = true;

    /**
     * Fields
     * Override field names
     *
     * @var array
     */
    public $fields = array();

    /**
     * Sort By
     * Sort CSV by this field
     *
     * @var string|null
     */
    public $sort_by = null;

    /**
     * Sort Reverse
     * Reverse the sort function
     *
     * @var bool
     */
    public $sort_reverse = false;

    /**
     * Sort Type
     * Sort behavior passed to sort methods
     *
     * regular = SORT_REGULAR
     * numeric = SORT_NUMERIC
     * string  = SORT_STRING
     *
     * @var string|null
     */
    public $sort_type = null;

    /**
     * Delimiter
     * Delimiter character
     *
     * @var string
     */
    public $delimiter = ',';

    /**
     * Enclosure
     * Enclosure character
     *
     * @var string
     */
    public $enclosure = '"';

    /**
     * Enclose All
     * Force enclosing all columns
     *
     * @var bool
     */
    public $enclose_all = false;

    /**
     * Conditions
     * Basic SQL-Like conditions for row matching
     *
     * @var string|null
     */
    public $conditions = null;

    /**
     * Offset
     * Number of rows to ignore from beginning of data
     *
     * @var int|null
     */
    public $offset = null;

    /**
     * Limit
     * Limits the number of returned rows to the specified amount
     *
     * @var int|null
     */
    public $limit = null;

    /**
     * Auto Depth
     * Number of rows to analyze when attempting to auto-detect delimiter
     *
     * @var int
     */
    public $auto_depth = 15;

    /**
     * Auto Non Charts
     * Characters that should be ignored when attempting to auto-detect delimiter
     *
     * @var string
     */
    public $auto_non_chars = "a-zA-Z0-9\n\r";

    /**
     * Auto Preferred
     * preferred delimiter characters, only used when all filtering method
     * returns multiple possible delimiters (happens very rarely)
     *
     * @var string
     */
    public $auto_preferred = ",;\t.:|";

    /**
     * Convert Encoding
     * Should we convert the csv encoding?
     *
     * @var bool
     */
    public $convert_encoding = false;

    /**
     * Input Encoding
     * Set the input encoding
     *
     * @var string
     */
    public $input_encoding = 'ISO-8859-1';

    /**
     * Output Encoding
     * Set the output encoding
     *
     * @var string
     */
    public $output_encoding = 'ISO-8859-1';

    /**
     * Whether to use mb_convert_encoding() instead of iconv().
     *
     * The former is platform-independent whereas the latter is the traditional
     * default go-to solution.
     *
     * @var bool (if false, iconv() is used)
     */
    public $use_mb_convert_encoding = false;

    /**
     * Linefeed
     * Line feed characters used by unparse, save, and output methods
     *
     * @var string
     */
    public $linefeed = "\r";

    /**
     * Output Delimiter
     * Sets the output delimiter used by the output method
     *
     * @var string
     */
    public $output_delimiter = ',';

    /**
     * Output filename
     * Sets the output filename
     *
     * @var string
     */
    public $output_filename = 'data.csv';

    /**
     * Keep File Data
     * keep raw file data in memory after successful parsing (useful for debugging)
     *
     * @var bool
     */
    public $keep_file_data = false;

    /**
     * Internal variables
     */

    /**
     * File
     * Current Filename
     *
     * @var string
     */
    public $file;

    /**
     * File Data
     * Current file data
     *
     * @var string
     */
    public $file_data;

    /**
     * Error
     * Contains the error code if one occurred
     *
     * 0 = No errors found. Everything should be fine :)
     * 1 = Hopefully correctable syntax error was found.
     * 2 = Enclosure character (double quote by default)
     *     was found in non-enclosed field. This means
     *     the file is either corrupt, or does not
     *     standard CSV formatting. Please validate
     *     the parsed data yourself.
     *
     * @var int
     */
    public $error = 0;

    /**
     * Error Information
     * Detailed error information
     *
     * @var array
     */
    public $error_info = array();

    /**
     * Titles
     * CSV titles if they exists
     *
     * @var array
     */
    public $titles = array();

    /**
     * Data
     * Two dimensional array of CSV data
     *
     * @var array
     */
    public $data = array();

    use Encoding;
    use Separator;
    use Parse;
    use Write;

    /**
     * Constructor
     * Class constructor
     *
     * @param  string|null  $input          The CSV string or a direct filepath
     * @param  integer|null $offset         Number of rows to ignore from the beginning of  the data
     * @param  integer|null $limit          Limits the number of returned rows to specified amount
     * @param  string|null  $conditions     Basic SQL-like conditions for row matching
     * @param  null|true    $keep_file_data Keep raw file data in memory after successful parsing (useful for debugging)
     */
    public function __construct($input = null, $offset = null, $limit = null, $conditions = null, $keep_file_data = null) {
        if (!is_null($offset)) {
            $this->offset = $offset;
        }

        if (!is_null($limit)) {
            $this->limit = $limit;
        }

        if (!is_null($conditions)) {
            $this->conditions = $conditions;
        }

        if (!is_null($keep_file_data)) {
            $this->keep_file_data = $keep_file_data;
        }

        if (!empty($input)) {
            $this->parse($input);
        }
    }

    /**
     * Parse
     * Parse a CSV file or string
     *
     * @param  string|null $input      The CSV string or a direct filepath
     * @param  integer     $offset     Number of rows to ignore from the beginning of  the data
     * @param  integer     $limit      Limits the number of returned rows to specified amount
     * @param  string      $conditions Basic SQL-like conditions for row matching
     *
     * @return bool True on success
     */
    public function parse($input = null, $offset = null, $limit = null, $conditions = null) {
        if (is_null($input)) {
            $input = $this->file;
        }

        if (!empty($input)) {
            if (!is_null($offset)) {
                $this->offset = $offset;
            }

            if (!is_null($limit)) {
                $this->limit = $limit;
            }

            if (!is_null($conditions)) {
                $this->conditions = $conditions;
            }

            if (strlen($input) <= PHP_MAXPATHLEN && is_readable($input)) {
                $this->data = $this->parse_file($input);
            } else {
                $this->file_data = &$input;
                $this->data = $this->parse_string();
            }

            if ($this->data === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Save
     * Save changes, or write a new file and/or data
     *
     * @param  string $file   File location to save to
     * @param  array  $data   2D array of data
     * @param  bool   $append Append current data to end of target CSV, if file exists
     * @param  array  $fields Field names
     *
     * @return bool
     */
    public function save($file = '', $data = array(), $append = false, $fields = array()) {
        if (empty($file)) {
            $file = &$this->file;
        }

        $mode = $append ? 'ab' : 'wb';
        $is_php = preg_match('/\.php$/i', $file) ? true : false;

        return $this->_wfile($file, $this->unparse($data, $fields, $append, $is_php), $mode);
    }

    /**
     * Output
     * Generate a CSV based string for output.
     *
     * @param  string|null $filename   If a filename is specified here or in the
     *                                 object, headers and data will be output
     *                                 directly to browser as a downloadable
     *                                 file.
     * @param  array[]     $data       2D array with data
     * @param  array       $fields     Field names
     * @param  string|null $delimiter  character used to separate data
     *
     * @return string  The resulting CSV string
     */
    public function output($filename = null, $data = array(), $fields = array(), $delimiter = null) {
        if (empty($filename)) {
            $filename = $this->output_filename;
        }

        if ($delimiter === null) {
            $delimiter = $this->output_delimiter;
        }

        $flat_string = $this->unparse($data, $fields, null, null, $delimiter);

        if (!is_null($filename)) {
            header('Content-type: application/csv');
            header('Content-Length: ' . strlen($flat_string));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Content-Disposition: attachment; filename="' . $filename . '"; modification-date="' . date('r') . '";');

            echo $flat_string;
        }

        return $flat_string;
    }

    /**
     * Auto
     * Auto-Detect Delimiter: Find delimiter by analyzing a specific number of
     * rows to determine most probable delimiter character
     *
     * @param  string|null $file         Local CSV file
     * @param  bool        $parse        True/false parse file directly
     * @param  int         $search_depth Number of rows to analyze
     * @param  string      $preferred    Preferred delimiter characters
     * @param  string|null $enclosure    Enclosure character, default is double quote (").
     *
     * @return string The detected field delimiter
     */
    public function auto($file = null, $parse = true, $search_depth = null, $preferred = null, $enclosure = null) {
        if (is_null($file)) {
            $file = $this->file;
        }

        if (empty($search_depth)) {
            $search_depth = $this->auto_depth;
        }

        if (is_null($enclosure)) {
            $enclosure = $this->enclosure;
        } else {
            $this->enclosure = $enclosure;
        }

        if (is_null($preferred)) {
            $preferred = $this->auto_preferred;
        }

        if (empty($this->file_data)) {
            if ($this->_check_data($file)) {
                $data = &$this->file_data;
            } else {
                return false;
            }
        } else {
            $data = &$this->file_data;
        }

        if (!$this->_detect_and_remove_sep_row_from_data($data)) {
            $this->_guess_delimiter($search_depth, $preferred, $enclosure, $data);
        }

        // parse data
        if ($parse) {
            $this->data = $this->parse_string();
        }

        return $this->delimiter;
    }

    /**
     * Create CSV data string from array
     *
     * @param array[]     $data       2D array with data
     * @param array       $fields     field names
     * @param bool        $append     if true, field names will not be output
     * @param bool        $is_php     if a php die() call should be put on the
     *                                first line of the file, this is later
     *                                ignored when read.
     * @param string|null $delimiter  field delimiter to use
     *
     * @return string CSV data
     */
    public function unparse($data = array(), $fields = array(), $append = false, $is_php = false, $delimiter = null) {
        if (!is_array($data) || empty($data)) {
            $data = &$this->data;
        }

        if (!is_array($fields) || empty($fields)) {
            $fields = &$this->titles;
        }

        if ($delimiter === null) {
            $delimiter = $this->delimiter;
        }

        $string = $is_php ? "<?php header('Status: 403'); die(' '); ?>" . $this->linefeed : '';
        $entry = array();

        // create heading
        if ($this->heading && !$append && !empty($fields)) {
            foreach ($fields as $key => $column_name) {
                $entry[] = $this->_enclose_value($column_name, $delimiter);
            }

            $string .= implode($delimiter, $entry) . $this->linefeed;
            $entry = array();
        }

        // create data
        foreach ($data as $key => $row) {
            foreach ($row as $cell_value) {
                $entry[] = $this->_enclose_value($cell_value, $delimiter);
            }

            $string .= implode($delimiter, $entry) . $this->linefeed;
            $entry = array();
        }

        if ($this->convert_encoding) {
            $string = iconv($this->input_encoding, $this->output_encoding, $string);
        }

        return $string;
    }
}
