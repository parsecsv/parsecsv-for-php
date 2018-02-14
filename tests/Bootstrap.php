<?php

$dir = realpath(__DIR__);
defined('BASE') OR define('BASE', dirname($dir) . '/');

recursivelyIncludeFiles(BASE . 'extensions');

require_once BASE . 'parsecsv.lib.php';

if (!class_exists('PHPUnit\Framework\TestCase')) {
    // we run on an older PHPUnit version without namespaces.
    require_once __DIR__ . '/PHPUnit_Framework_TestCase.inc.php';
}

function recursivelyIncludeFiles($pathName){
    $dirIterator = new RecursiveDirectoryIterator($pathName);
    foreach ($dirIterator as $dir) {
        if ($dir->isFile() && $dir->getExtension() === 'php') {
            require_once $dir->getPathname();
        }
    }
}
