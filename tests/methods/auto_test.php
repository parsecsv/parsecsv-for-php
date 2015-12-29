<?php

class auto_Test extends PHPUnit_Framework_TestCase
{
    public function autoQuotesDataProvider()
    {
        return array(
            array('tests/methods/fixtures/auto-double-enclosure.csv', '"'),
            array('tests/methods/fixtures/auto-single-enclosure.csv', "'"),
        );
    }

    /**
     * @dataProvider autoQuotesDataProvider
     * 
     * @param string $file
     * @param string $enclosure
     */
    public function testAutoQuotes($file, $enclosure)
    {
        $csv = new parseCSV();
        $csv->auto($file, true, null, null, $enclosure);
        $this->assertArrayHasKey('column1', $csv->data[0], 'Data parsed incorrectly with enclosure ' . $enclosure);
        $this->assertEquals('value1', $csv->data[0]['column1'], 'Data parsed incorrectly with enclosure ' . $enclosure);
    }
}
