<?php

trait Encoding {
    /**
     * Encoding
     * Convert character encoding
     *
     * @param  [string] $input  Input character encoding, uses default if left blank
     * @param  [string] $output Output character encoding, uses default if left blank
     */
    public function encoding($input = null, $output = null) {
        $this->convert_encoding = true;
        if (!is_null($input)) {
            $this->input_encoding = $input;
        }

        if (!is_null($output)) {
            $this->output_encoding = $output;
        }
    }
}
