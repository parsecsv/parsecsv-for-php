<?php

$dir = realpath(__DIR__);
defined('BASE') OR define('BASE', dirname($dir) . '/');

require_once BASE . 'ParseCsvForPhp.php';

if (!class_exists('PHPUnit\Framework\TestCase')) {
    // we run on an older PHPUnit version without namespaces.
    require_once __DIR__ . '/PHPUnit_Framework_TestCase.inc.php';
}

require_once BASE . 'tests/properties/BaseClass.php';
