<?php

class worthless_properties_Test extends PHPUnit_Framework_TestCase {
    /**
     * CSV
     * The parseCSV object
     *
     * @access protected
     * @var [parseCSV]
     */
    protected $csv = null;

    /**
     * Reflection Object
     * The reflection class object
     *
     * @access protected
     * @var [ReflectionClass]
     */
    protected $reflection = null;

    /**
     * Reflection Properties
     * The reflected class properties
     *
     * @access protected
     */
    protected $properties = null;

    /**
     * Setup
     * Setup our test environment objects
     *
     * @access public
     */
    public function setUp() {
        //setup parse CSV
        $this->csv = new parseCSV();

        //setup the reflection class
        $this->reflection = new ReflectionClass($this->csv);

        //setup the reflected class properties
        $this->properties = $this->reflection->getProperties();
    }

    /**
     * Tear down
     * Tear down our test environment objects
     *
     * @access public
     */
    public function tearDown() {
        $this->csv = null;
        $this->reflection = null;
        $this->properties = null;
    }

    /**
     * test_propertiesCount
     * Counts the number of properties to make sure we didn't add or
     * subtract any without thinking
     *
     * @access public
     */
    public function test_propertiesCount() {
        $this->assertCount(27,$this->properties);
    }

    /**
     * test_property_names
     * We have an expected set of properties that should exists
     * Make sure our expected number of properties matches the real
     * count of properties and also check to make sure our expected
     * properties exists within the class
     *
     * @access public
     */
    public function test_property_names() {
        //set our expected properties name(s)
        $names = array(
            'heading',
            'fields',
            'sort_by',
            'sort_reverse',
            'sort_type',
            'delimiter',
            'enclosure',
            'enclose_all',
            'conditions',
            'offset',
            'limit',
            'auto_depth',
            'auto_non_chars',
            'auto_preferred',
            'convert_encoding',
            'input_encoding',
            'output_encoding',
            'linefeed',
            'output_delimiter',
            'output_filename',
            'keep_file_data',
            'file',
            'file_data',
            'error',
            'error_info',
            'titles',
            'data'
        );

        //find our real properties
        $real_properties = array();
        for ($a=0; $a<count($this->properties); $a++) {
            $real_properties[] = $this->properties[$a]->getName();
        }

        //lets make sure our expected matches the number of real properties
        $this->assertCount(count($names),$this->properties);

        //lets loop through our expected to make sure they exists
        for ($a=0; $a<count($names); $a++) {
            $this->assertTrue(in_array($names[$a],$real_properties));
        }
    }

    /**
     * test_count_public_properties
     * We at this point only have public properties so
     * lets verify all properties are public
     *
     * @access public
     */
    public function test_count_public_properties() {
        $counter = 0;

        for ($a=0; $a<count($this->properties); $a++) {
            if ($this->properties[$a]->isPublic() === true) {
                $counter++;
            }
        }

        $this->assertCount($counter,$this->properties);
    }
}
