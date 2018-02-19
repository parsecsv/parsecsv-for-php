<?php
namespace ParseCsv\extensions;

trait DatatypeTrait {

    /**
     * Datatypes
     * Datatypes of CSV data-columns
     *
     * @access public
     * @var array
     */
    public $data_types = [];

    /**
     * Check data type for one column.
     * Check for most commonly data type for one column.
     *
     * @access private
     *
     * @param  array $datatypes
     *
     * @return string|false
     */
    private function getMostFrequentDataypeForColumn($datatypes) {
        array_filter($datatypes);     

        // workaround because array_count_values($datatypes) does not work anymore :-(
        foreach ($datatypes as $type) {
            $ids = array_keys($datatypes, $type);
            $typesFreq[$type] = count($ids);

            $datatypes = array_diff_key($datatypes, array_flip($ids));
        }
        arsort($typesFreq);
        reset($typesFreq);

        return key($typesFreq);

    }

    /**
     * Check data type foreach Column
     * Check data type for each column and returns the most commonly.
     *
     * @access public
     *
     * @return array|bool
     */
    public function getDatatypes() {
        if (empty($this->data)) {
            $this->data = $this->parse_string();
        }

        $result = [];
        foreach ($this->titles as $cName) {
            $column = array_column($this->data, $cName);

            $cDatatypes = array_map('ParseCsv\enums\DatatypeEnum::getValidTypeFromSample', $column);

            $result[$cName] = $this->getMostFrequentDataypeForColumn($cDatatypes);
        }

        $this->data_types = $result;

        return !empty($this->data_types) ? $this->data_types : [];
    }
}
