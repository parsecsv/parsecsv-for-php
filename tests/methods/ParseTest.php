<?php

class ParseTest extends PHPUnit\Framework\TestCase {

    /**
     * @access protected
     * @var ParseCsvForPhp
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

    public function testSingleColumnWithZeros() {
        $this->csv->delimiter = null;
        $this->csv->parse("URL\nhttp://www.amazon.com/ROX-Ice-Ball-Maker-Original/dp/B00MX59NMQ/ref=sr_1_1?ie=UTF8&qid=1435604374&sr=8-1&keywords=rox,+ice+molds");
        $row = array_pop($this->csv->data);
        $expected_data = ['URL' => 'http://www.amazon.com/ROX-Ice-Ball-Maker-Original/dp/B00MX59NMQ/ref=sr_1_1?ie=UTF8&qid=1435604374&sr=8-1&keywords=rox,+ice+molds'];
        $this->assertEquals($expected_data, $row);
    }

    public function testAllNumericalCsv() {
        $this->csv->heading = false;
        $sInput = "86545235689\r\n34365587654\r\n13469874576";
        $this->assertEquals(false, $this->csv->auto($sInput));
        $this->assertEquals(null, $this->csv->delimiter);
        $expected_data = explode("\r\n", $sInput);
        $actual_data = array_map('reset', $this->csv->data);
        $this->assertEquals($expected_data, $actual_data);
    }

    public function testMissingEndingLineBreak() {
        $this->csv->heading = false;
        $this->csv->enclosure = '"';
        $sInput = "86545235689,a\r\n34365587654,b\r\n13469874576,\"c\r\nd\"";
        $expected_data = [86545235689, 34365587654, 13469874576];
        $actual_data = $this->csv->parse_string($sInput);
        $actual_column = array_map('reset', $actual_data);
        $this->assertEquals($expected_data, $actual_column);
        $this->assertEquals([
            'a',
            'b',
            "c\r\nd",
        ], array_map('next', $actual_data));
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

    public function test_Piwik_data() {
        if (!function_exists('array_column')) {
            // function only available in PHP >= 5.5
            return;
        }
        $this->csv->use_mb_convert_encoding = true;
        $this->csv->output_encoding = 'UTF-8';
        $this->csv->auto(__DIR__ . '/../example_files/Piwik_API_download.csv');
        $aAction27 = array_column($this->csv->data, 'url (actionDetails 27)');
        $this->assertEquals([
            'http://application/_Main/_GraphicMeanSTD_MDI/btnConfBandOptions',
            '',
            '',
        ], $aAction27);

        $aCity = array_column($this->csv->data, 'city');
        $this->assertEquals([
            'São Paulo',
            'Johannesburg',
            '',
        ], $aCity);
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

    public function autoQuotesDataProvider() {
        return array(
            array('auto-double-enclosure.csv', '"'),
            array('auto-single-enclosure.csv', "'"),
        );
    }

    /**
     * @dataProvider autoQuotesDataProvider
     *
     * @param string $file
     * @param string $enclosure
     */
    public function testAutoQuotes($file, $enclosure) {
        $csv = new ParseCsvForPhp();
        $csv->auto(__DIR__ . '/../example_files/' . $file, true, null, null, $enclosure);
        $this->assertArrayHasKey('column1', $csv->data[0], 'Data parsed incorrectly with enclosure ' . $enclosure);
        $this->assertEquals('value1', $csv->data[0]['column1'], 'Data parsed incorrectly with enclosure ' . $enclosure);
    }
}
