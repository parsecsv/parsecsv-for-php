<?php

namespace ParseCsv\tests\methods;

use ParseCsv\Csv;
use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase {

    /**
     * @runInSeparateProcess because download.php uses header()
     */
    public function testOutputWithFourParameters() {
        $csv = new Csv();
        $data = [0 => ['a', 'b', 'c'], 1 => ['d', 'e', 'f']];
        $fields = ['col1', 'col2', 'col3'];
        $output = $csv->output('test.csv', $data, $fields, ',');
        $expected = "col1,col2,col3\ra,b,c\rd,e,f\r";
        self::assertEquals($expected, $output);
    }
}
