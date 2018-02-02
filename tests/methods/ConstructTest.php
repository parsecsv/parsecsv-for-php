<?php

class ConstructTest extends PHPUnit\Framework\TestCase {

    /**
     * @access protected
     * @var ParseCsvForPhp object
     */
    protected $csv = null;

    public function test_offset_param() {
        $offset = 10;
        $this->csv = new ParseCsvForPhp(null, $offset);
        $this->assertTrue(is_numeric($this->csv->offset));
        $this->assertEquals($offset, $this->csv->offset);
    }

    public function test_limit_param() {
        $limit = 10;
        $this->csv = new ParseCsvForPhp(null, null, $limit);
        $this->assertTrue(is_numeric($this->csv->limit));
        $this->assertEquals($limit, $this->csv->limit);
    }

    public function test_conditions_param() {
        $conditions = 'some column NOT value';
        $this->csv = new ParseCsvForPhp(null, null, null, $conditions);
        $this->assertTrue(is_string($this->csv->conditions));
        $this->assertEquals($conditions, $this->csv->conditions);
    }

    public function test_keep_file_data_param() {
        $keep = true;
        $this->csv = new ParseCsvForPhp(null, null, null, null, $keep);
        $this->assertTrue(is_bool($this->csv->keep_file_data));
        $this->assertEquals($keep, $this->csv->keep_file_data);
    }

    public function test_input_param() {
        $csv = "col1,col2,col3\r\nval1,val2,val3\r\nval1A,val2A,val3A\r\n";
        $this->csv = new ParseCsvForPhp($csv, null, null, null, true);
        $this->assertTrue(is_string($this->csv->file_data));
        $this->assertEquals($csv, $this->csv->file_data);
    }
}
