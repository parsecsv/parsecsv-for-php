<?php

class parse_test extends PHPUnit_Framework_TestCase {

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
        $this->csv->heading = FALSE;
        $success = $this->csv->parse(str_repeat($content . ';', 500));
        $this->assertEquals(TRUE, $success);

        $row = array_pop($this->csv->data);
        $expected_data = array_fill(0, 500, $content);
        $expected_data [] = '';
        $this->assertEquals($expected_data, $row);
    }
}
