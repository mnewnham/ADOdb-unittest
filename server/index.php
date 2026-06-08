<?php

/**
 * This is the unittest connection bootstrap file
 *
 * This file is part of ADOdb-unittest, a PHPUnit test suite for
 * the ADOdb Database Abstraction Layer library for PHP.
 *
 * PHP version 8.0.0+
 *
 * @category  Library
 * @package   ADOdb-unittest
 * @author    Mark Newnham <mnewnham@github.com>
 * @copyright 2025,2026 Mark Newnham
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 *
 * @link https://github.com/mnewnham/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

$ADOdbSettings = [];
/*
* Load currently active database $db becomes active
*/
ob_start();
require_once '../tools/dbconnector.php';
ob_end_clean();
require_once $ADOdbSettings['directory'] . '/session/adodb-session2.php';

$data = json_decode(base64_decode($_REQUEST['data']));

require_once 'server/' . $data->class . '.php';


$className = $data->class;
$testName  = $data->test;
$testData  = $data->data;

$class = new $className();
$test  = $class->$testName($testData);
