<?php

$dir = realpath(__DIR__);
defined('BASE') OR define('BASE', dirname($dir) . '/');
require_once BASE . 'core/Encoding.php';
require_once BASE . 'core/Separator.php';
require_once BASE . 'core/Parse.php';
require_once BASE . 'core/Write.php';
require_once BASE . 'parsecsv.lib.php';

if (!class_exists('PHPUnit\Framework\TestCase')) {
    // we run on an older PHPUnit version without namespaces.
    require_once __DIR__ . '/PHPUnit_Framework_TestCase.inc.php';
}
