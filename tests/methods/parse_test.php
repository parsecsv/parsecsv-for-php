<?php

class parse_methods_Test extends PHPUnit_Framework_TestCase {
    /**
     * CSV
     * The parseCSV object
     *
     * @access protected
     * @var [parseCSV]
     */
    protected $csv = null;

    protected $input = "col1,col2,col3\r\nval1,val2,val3";
    protected $path  = null;

    /**
     * Setup
     * Setup our test environment objects
     *
     * @access public
     */
    public function setUp() {
        //setup parse CSV
        $this->csv = new parseCSV(null,null,null,null,true);

        if (is_null($this->path)) {
            $this->path = BASE.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'csv_files'.DIRECTORY_SEPARATOR.'5_rows_normal.csv';
        }
    }

    /**
     * Tear down
     * Tear down our test environment objects
     *
     * @access public
     */
    public function tearDown() {
        $this->csv = null;
    }

    public function test_input_param() {

        $this->csv->parse($this->input);
        $this->assertTrue(is_string($this->csv->file_data));
        $this->assertEquals($this->input,$this->csv->file_data);
    }

    public function test_offset_param() {
        $offset = 100;
        $this->csv->parse($this->input,$offset);
        $this->assertTrue(is_numeric($this->csv->offset));
        $this->assertEquals($offset,$this->csv->offset);
    }

    public function test_limit_param() {
        $limit = 100;
        $this->csv->parse($this->input,null,$limit);
        $this->assertTrue(is_numeric($this->csv->limit));
        $this->assertEquals($limit,$this->csv->limit);
    }

    public function test_conditions_param() {
        $condition = "Author NOT Joe";
        $this->csv->parse($this->input,null,null,$condition);
        $this->assertTrue(is_string($this->csv->conditions));
        $this->assertEquals($condition,$this->csv->conditions);
    }

    public function test_preset_input() {
        $this->csv->file = $this->path;
        $exp =$this->csv->parse();
        $this->assertTrue($exp);
    }

    public function test_readable_input() {
        $this->csv->parse($this->path);
        $this->assertTrue(is_string($this->csv->file_data));
    }

/*
    public function test_false_return_on_parse() {
        $path = BASE.'tests'.DIRECTORY_SEPARATOR.'csv_files'.DIRECTORY_SEPARATOR.'empty.csv';
        $exp = $this->csv->parse($path);
        $this->assertFalse($exp);
    }
*/
}
