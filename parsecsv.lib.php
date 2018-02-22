<?php

// This file should not be used at all! It purely exists to reduce the
// maintenance burden for existing code using this repo.

// Check if people used Composer to include this project in theirs
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die(
        "Please run `composer dump-autoload` to build the autoloader.\n\n" .
        "Actually, you should consider not including/requiring this file \n" .
        "  " . __FILE__ . "\n" .
        "Just run `composer require parsecsv/php-parsecsv` and look at the \n" .
        "'examples' directory of this repository."
    );
}

require __DIR__ . '/vendor/autoload.php';

// This wrapper class should not be used by new projects. Please look at the
// examples to find the up-to-date way of using this repo.
class parseCSV extends ParseCsv\Csv {

}
