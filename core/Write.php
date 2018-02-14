<?php

trait Write {
    /**
     * Write to local file
     *
     * @param   string $file    local filename
     * @param   string $content data to write to file
     * @param   string $mode    fopen() mode
     * @param   int    $lock    flock() mode
     *
     * @return  true or false
     */
    protected function _wfile($file, $content = '', $mode = 'wb', $lock = LOCK_EX) {
        if ($fp = fopen($file, $mode)) {
            flock($fp, $lock);
            $re = fwrite($fp, $content);
            $re2 = fclose($fp);

            if ($re !== false && $re2 !== false) {
                return true;
            }
        }

        return false;
    }
}
