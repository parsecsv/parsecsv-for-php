<?php

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
     * Check data type
     * Check for possible data types for one field value string.
     *
     * @access private
     *
     * @param string $value cell value
     *
     * @return string
     */
    private function getDatatypeFromString($value) {
        $value = trim((string) $value);

        if (empty($value)) {
            return 'unknown';
        }

        if (preg_match('/^(?i:true|false)$/', $value)) {
            return 'boolean';
        }

        if (preg_match('/^[-+]?[0-9]\d*$/', $value)) {
            return 'integer';
        }

        if (preg_match('/(^[+-]?$)|(^[+-]?[0-9]+([,.][0-9])?[0-9]*(e[+-]?[0-9]+)?$)/', $value)) {
            return 'float';
        }

        if ((bool) strtotime($value)) {
            return 'date';
        }

        return 'string';
    }

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
        unset($datatypes['unknown']);

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
     * @uses getDatatypeFromString
     *
     * @return array|bool
     */
    public function getDatatypes() {
        if (empty($this->data)) {
            $this->data = $this->parse_string();
        }
        if (!is_array($this->data)) {
            throw new \Exception('No data set yet.');
        }

        $result = [];
        foreach ($this->titles as $cName) {
            $column = array_column($this->data, $cName);
            $cDatatypes = array_map([$this, 'getDatatypeFromString'], $column);

            $result[$cName] = $this->getMostFrequentDataypeForColumn($cDatatypes);
        }

        $this->data_types = $result;

        return !empty($this->data_types) ? $this->data_types : false;
    }
}
