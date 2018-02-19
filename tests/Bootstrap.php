<?php

$dir = realpath(__DIR__);
defined('BASE') OR define('BASE', dirname($dir) . '/');

$dirIterator = new RecursiveDirectoryIterator(BASE . 'core');
foreach ($dirIterator as $dir) {
    if ($dir->isFile() && $dir->getExtension() === 'php') {
        require_once $dir->getPathname();
    }
}

require_once BASE . 'parsecsv.lib.php';

if (!class_exists('PHPUnit\Framework\TestCase')) {
    // we run on an older PHPUnit version without namespaces.
    require_once __DIR__ . '/PHPUnit_Framework_TestCase.inc.php';
}

require_once BASE . 'tests/properties/BaseClass.php';
