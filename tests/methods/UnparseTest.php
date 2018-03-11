<?php
namespace ParseCsv\tests\methods;

use ParseCsv\Csv;
use PHPUnit\Framework\TestCase;


class UnparseTest extends Testcase {
    /** @var Csv */
    private $csv;

    /**
     * Setup our test environment objects; will be called before each test.
     */
    public function setUp() {
        $this->csv = new Csv();
        $this->csv->auto(__DIR__ . '/fixtures/auto-double-enclosure.csv');
    }

    public function testUnparseDefault() {
        $expected = "column1,column2\rvalue1,value2\rvalue3,value4\r";
        $this->unparseAndCompare($expected);
    }

    public function testUnparseDefaultWithoutHeading(){
        $this->csv->heading = false;
        $this->csv->auto(__DIR__ . '/fixtures/auto-double-enclosure.csv');
        $expected = "column1,column2\rvalue1,value2\rvalue3,value4\r";
        $this->unparseAndCompare($expected);

    }

    public function testUnparseRenameFields() {
        $expected = "C1,C2\rvalue1,value2\rvalue3,value4\r";
        $this->unparseAndCompare($expected, array("C1", "C2"));
    }

    public function testReorderFields() {
        $expected = "column2,column1\rvalue2,value1\rvalue4,value3\r";
        $this->unparseAndCompare($expected, array("column2", "column1"));
    }

    public function testSubsetFields() {
        $expected = "column1\rvalue1\rvalue3\r";
        $this->unparseAndCompare($expected, array("column1"));
    }

    public function testReorderAndRenameFields() {
        $fields = array(
            'column2' => 'C2',
            'column1' => 'C1',
        );
        $expected = "C2,C1\rvalue2,value1\rvalue4,value3\r";
        $this->unparseAndCompare($expected, $fields);
    }

    private function unparseAndCompare($expected, $fields = array()) {
        $str = $this->csv->unparse($this->csv->data, $fields);
        $this->assertEquals($expected, $str);
    }

}
