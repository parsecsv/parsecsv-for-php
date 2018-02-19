<?php
chdir(__DIR__ . '/..');
if (!file_exists('vendor/autoload.php')) {
    `composer dump-autoload`;
}

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('PHPUnit\Framework\TestCase') && class_exists('PHPUnit_Framework_TestCase')) {
    // we run on an older PHPUnit version without namespaces.
    require_once __DIR__ . '/PHPUnit_Framework_TestCase.inc.php';
}

