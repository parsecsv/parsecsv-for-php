<?php

$dir = realpath(dirname(__FILE__));
defined('BASE') OR define('BASE', realpath($dir.'/../').DIRECTORY_SEPARATOR);

require_once BASE.'parsecsv.lib.php';
