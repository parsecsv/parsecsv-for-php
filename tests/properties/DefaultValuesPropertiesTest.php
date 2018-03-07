<?php

namespace ParseCsv\tests\properties;

use ParseCsv\Csv;
use PHPUnit\Framework\TestCase;

class DefaultValuesPropertiesTest extends TestCase {

    /**
     * CSV
     * The parseCSV object
     *
     * @access protected
     * @var Csv
     */
    protected $csv = null;

    /**
     * Setup
     * Setup our test environment objects
     *
     * @access public
     */
    public function setUp() {
        //setup parse CSV
        $this->csv = new Csv();
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

    public function test_heading_default() {
        $this->assertTrue(is_bool($this->csv->heading));
        $this->assertTrue($this->csv->heading);
    }

    public function test_fields_default() {
        $this->assertTrue(is_array($this->csv->fields));
        $this->assertCount(0, $this->csv->fields);
    }

    public function test_sort_by_default() {
        $this->assertNull($this->csv->sort_by);
    }

    public function test_sort_reverse_default() {
        $this->assertTrue(is_bool($this->csv->sort_reverse));
        $this->assertFalse($this->csv->sort_reverse);
    }

    public function test_sort_type_default() {
        $this->assertEquals('regular', $this->csv->sort_type);
    }

    public function test_delimiter_default() {
        $this->assertTrue(is_string($this->csv->delimiter));
        $this->assertEquals(',', $this->csv->delimiter);
    }

    public function test_enclosure_default() {
        $this->assertTrue(is_string($this->csv->enclosure));
        $this->assertEquals('"', $this->csv->enclosure);
    }

    public function test_enclose_all_default() {
        $this->assertTrue(is_bool($this->csv->enclose_all));
        $this->assertFalse($this->csv->enclose_all);
    }

    public function test_conditions_default() {
        $this->assertNull($this->csv->conditions);
    }

    public function test_offset_default() {
        $this->assertNull($this->csv->offset);
    }

    public function test_limit_default() {
        $this->assertNull($this->csv->limit);
    }

    public function test_auto_depth_default() {
        $this->assertTrue(is_numeric($this->csv->auto_depth));
        $this->assertEquals(15, $this->csv->auto_depth);
    }

    public function test_auto_non_chars_default() {
        $this->assertTrue(is_string($this->csv->auto_non_chars));
        $this->assertEquals("a-zA-Z0-9\n\r", $this->csv->auto_non_chars);
    }

    public function test_auto_preferred_default() {
        $this->assertTrue(is_string($this->csv->auto_preferred));
        $this->assertEquals(",;\t.:|", $this->csv->auto_preferred);
    }

    public function test_convert_encoding_default() {
        $this->assertTrue(is_bool($this->csv->convert_encoding));
        $this->assertFalse($this->csv->convert_encoding);
    }

    public function test_input_encoding_default() {
        $this->assertTrue(is_string($this->csv->input_encoding));
        $this->assertEquals('ISO-8859-1', $this->csv->input_encoding);
    }

    public function test_output_encoding_default() {
        $this->assertTrue(is_string($this->csv->output_encoding));
        $this->assertEquals('ISO-8859-1', $this->csv->output_encoding);
    }

    public function test_linefeed_default() {
        $this->assertTrue(is_string($this->csv->linefeed));
        $this->assertEquals("\r", $this->csv->linefeed);
    }

    public function test_output_delimiter_default() {
        $this->assertTrue(is_string($this->csv->output_delimiter));
        $this->assertEquals(',', $this->csv->output_delimiter);
    }

    public function test_output_filename_default() {
        $this->assertTrue(is_string($this->csv->output_filename));
        $this->assertEquals('data.csv', $this->csv->output_filename);
    }

    public function test_keep_file_data_default() {
        $this->assertTrue(is_bool($this->csv->keep_file_data));
        $this->assertFalse($this->csv->keep_file_data);
    }

    public function test_file_default() {
        $this->assertNull($this->csv->file);
    }

    public function test_file_data_default() {
        $this->assertNull($this->csv->file_data);
    }

    public function test_error_default() {
        $this->assertTrue(is_numeric($this->csv->error));
        $this->assertEquals(0, $this->csv->error);
    }

    public function test_error_info_default() {
        $this->assertTrue(is_array($this->csv->error_info));
        $this->assertCount(0, $this->csv->error_info);
    }

    public function test_titles_default() {
        $this->assertTrue(is_array($this->csv->titles));
        $this->assertCount(0, $this->csv->titles);
    }

    public function test_data_default() {
        $this->assertTrue(is_array($this->csv->data));
        $this->assertCount(0, $this->csv->data);
    }
}
