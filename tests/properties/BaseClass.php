<?php

class BaseClass extends PHPUnit\Framework\TestCase {

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

    protected function _compareWithExpected($expected) {
        $this->csv->auto(__DIR__ . '/../../examples/_books.csv');
        $actual = array_map(function ($row) {
            return $row['title'];
        }, $this->csv->data);
        $this->assertEquals($expected, array_values($actual));
    }
}
