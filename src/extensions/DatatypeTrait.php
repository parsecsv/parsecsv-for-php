<?php
namespace CSV\extensions;

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

        $typesFreq = array_count_values($datatypes);
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

            $cDatatypes = array_map('CSV\enums\DatatypeEnum::getValidTypeFromSample', $column);

            $result[$cName] = $this->getMostFrequentDataypeForColumn($cDatatypes);
        }

        $this->data_types = $result;

        return !empty($this->data_types) ? $this->data_types : [];
    }
}
