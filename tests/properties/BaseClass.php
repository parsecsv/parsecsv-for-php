<?php

class BaseClass extends PHPUnit\Framework\TestCase {

    /**
     * @access protected
     * @var ParseCsvForPhp object
     */
    protected $csv;

    /**
     * Setup
     * Setup our test environment objects
     *
     * @access public
     */
    public function setUp() {
        $this->csv = new ParseCsvForPhp();
    }

    protected function _compareWithExpected($expected) {
        $this->csv->auto(__DIR__ . '/../../examples/_books.csv');
        $actual = array_map(function ($row) {
            return $row['title'];
        }, $this->csv->data);
        $this->assertEquals($expected, $actual);
    }
}
