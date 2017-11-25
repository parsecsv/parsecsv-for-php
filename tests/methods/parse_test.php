<?php

class parse_test extends PHPUnit\Framework\TestCase {

    /**
     * CSV
     * The parseCSV object
     *
     * @access protected
     * @var parseCSV
     */
    protected $csv;

    /**
     * Setup
     * Setup our test environment objects
     *
     * @access public
     */
    public function setUp() {
        $this->csv = new parseCSV();
    }

    public function test_parse() {
        // can we trick 'is_readable' into whining? See #67.
        $this->parse_repetitive_string('c:/looks/like/a/path');
        $this->parse_repetitive_string('http://looks/like/an/url');
    }

    private function parse_repetitive_string($content) {
        $this->csv->delimiter = ';';
        $this->csv->heading = false;
        $success = $this->csv->parse(str_repeat($content . ';', 500));
        $this->assertEquals(true, $success);

        $row = array_pop($this->csv->data);
        $expected_data = array_fill(0, 500, $content);
        $expected_data [] = '';
        $this->assertEquals($expected_data, $row);
    }

    public function test_sep_row_auto_detection_UTF8_no_BOM() {
        $this->_autoparse_magazine_file(
            __DIR__ . '/../example_files/UTF-8_sep_row_but_no_BOM.csv');
    }

    public function test_sep_row_auto_detection_UTF8() {
        $this->_autoparse_magazine_file(
            __DIR__ . '/../example_files/UTF-8_with_BOM_and_sep_row.csv');
    }

    public function test_sep_row_auto_detection_UTF16() {
        $this->_autoparse_magazine_file(
            __DIR__ . '/../example_files/UTF-16LE_with_BOM_and_sep_row.csv');
    }

    protected function _autoparse_magazine_file($file) {
        // This file (parse_test.php) is encoded in UTF-8, hence comparison will
        // fail unless we to this:
        $this->csv->output_encoding = 'UTF-8';

        $this->csv->auto($file);
        $this->assertEquals($this->_get_magazines_data(), $this->csv->data);
    }

    public function test_single_column() {
        $this->csv->auto(__DIR__ . '/../example_files/single_column.csv');
        $expected = [
            ['SMS' => '0444'],
            ['SMS' => '5555'],
            ['SMS' => '6606'],
            ['SMS' => '7777'],
        ];

        $this->assertEquals($expected, $this->csv->data);
    }

    protected function _get_magazines_data() {
        return [
            [
                'title' => 'Красивая кулинария',
                'isbn' => '5454-5587-3210',
                'publishedAt' => '21.05.2011',
            ],
            [
                'title' => 'The Wine Connoisseurs',
                'isbn' => '2547-8548-2541',
                'publishedAt' => '12.12.2011',
            ],
            [
                'title' => 'Weißwein',
                'isbn' => '1313-4545-8875',
                'publishedAt' => '23.02.2012',
            ],
        ];
    }
}
