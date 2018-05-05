<?php

namespace ParseCsv\tests\methods;

use ParseCsv\Csv;
use PHPUnit\Framework\TestCase;


class DataRowCountTest extends TestCase {

    /**
     * CSV
     * The CSV object
     *
     * @access protected
     * @var Csv
     */
    protected $csv;

    /**
     * Setup
     * Setup our test environment objects
     *
     * @access public
     */
    public function setUp() {
        $this->csv = new Csv();
    }

    public function countRowsProvider() {
        return [
            'auto-double-enclosure' => [
                'auto-double-enclosure.csv',
                2,
            ],
            'auto-single-enclosure' => [
                'auto-single-enclosure.csv',
                2,
            ],
            'UTF-8_sep_row' => [
                'datatype.csv',
                3,
            ],
        ];
    }

    /**
     * @dataProvider countRowsProvider
     *
     * @param string $file
     * @param int    $expectedRows
     */
    public function testGetTotalRowCountFromFile($file, $expectedRows) {
        $this->csv->heading = true;
        $this->csv->load_data(__DIR__ . '/fixtures/' . $file);
        $this->assertEquals($expectedRows, $this->csv->getTotalDataRowCount());
    }

    public function testGetTotalRowCountMissingEndingLineBreak() {
        $this->csv->heading = false;
        $this->csv->enclosure = '"';
        $sInput = "86545235689,a\r\n34365587654,b\r\n13469874576,\"c\r\nd\"";
        $this->csv->load_data($sInput);
        $this->assertEquals(3, $this->csv->getTotalDataRowCount());
    }

    public function testGetTotalRowCountSingleEnclosure() {
        $this->csv->heading = false;
        $this->csv->enclosure = "'";
        $sInput = "86545235689,a\r\n34365587654,b\r\n13469874576,\'c\r\nd\'";
        $this->csv->load_data($sInput);
        $this->assertEquals(3, $this->csv->getTotalDataRowCount());
    }

    public function testGetTotalRowCountSingleRow() {
        $this->csv->heading = false;
        $this->csv->enclosure = "'";
        $sInput = "86545235689";
        $this->csv->load_data($sInput);
        $this->assertEquals(1, $this->csv->getTotalDataRowCount());
    }
}
