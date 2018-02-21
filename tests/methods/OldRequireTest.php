<?php

namespace ParseCsv\tests\methods;

use PHPUnit\Framework\TestCase;

class OldRequireTest extends TestCase {

    protected function setUp() {
        rename('vendor/autoload.php', '__autoload');
    }

    protected function tearDown() {
        rename('__autoload', 'vendor/autoload.php');
    }

    /**
     * @runInSeparateProcess because download.php uses header()
     */
    public function testOldLibWithoutComposer() {

        file_put_contents('__eval.php', '<?php require "parsecsv.lib.php"; new \ParseCsv\Csv;');
        exec("php __eval.php", $output, $return_var);
        unlink('__eval.php');
        $this->assertEquals($output, []);
        $this->assertEquals(0, $return_var);
    }
}
