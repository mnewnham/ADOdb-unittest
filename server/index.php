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

$GLOBALS['ADOdbConnection']->stdOutputDebugging = false;
$GLOBALS['ADOdbConnection']->noHtmlDebugging    = true;

/**
 * A dummy function actived by the shutdown handler
 *
 * @param string|null $expiref The expiref
 * @param string      $key     The session key
 *
 * @return string
 */
function expiryNotificationTrigger(?string $expiref, string $key): string
{
    return $key;
}

$sessionParameters = $GLOBALS['TestingControl']['session'];
$testParams    = $GLOBALS['sessionTestParameters'];
$compression = $testParams['compression'];
$encryption  = $testParams['encryption'];


if (isset($sessionParameters['horde'])) {
    define('HORDE_BASE', $sessionParameters['horde']);
}

$compressionInclude = $compressionClass = '';

if (isset($sessionParameters['compress']) && $sessionParameters['compress']) {
    $compressionInclude =  $compression[$sessionParameters['compress']]['file'];
    $compressionClass   =  $compression[$sessionParameters['compress']]['class'];

    require_once $ADOdbSettings['directory'] . '/session/' . $compressionInclude;
}

$encryptionInclude = $encryptionClass = '';

require_once $ADOdbSettings['directory'] . '/session/crypt.inc.php';
require_once $ADOdbSettings['directory'] . '/session/adodb-session2.php';

if (isset($sessionParameters['encrypt']) && $sessionParameters['encrypt']) {
    $encryptionInclude =  $encryption[$sessionParameters['encrypt']]['file'];
    $encryptionClass   =  $encryption[$sessionParameters['encrypt']]['class'];

    require_once $ADOdbSettings['directory'] . '/session/' . $encryptionInclude;
}


if ($compressionClass) {
    ADODB_Session::filter(new $compressionClass());
}
if ($encryptionClass) {
    ADODB_Session::filter(new $encryptionClass());
}

ADODB_Session::expireNotify(['', 'expiryNotificationTrigger']);

require_once __DIR__ . '/ActivateCompatConnection.php';
$data = json_decode(base64_decode($_REQUEST['data']));

require_once 'server/' . $data->class . '.php';

$className = $data->class;
$testName  = $data->test;
$testData  = $data->data;

$class = new $className();
$test  = $class->$testName($testData);
