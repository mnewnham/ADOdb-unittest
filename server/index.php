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

/**
 * A dummy function actived by the shutdown handler
 *
 * @param string|null $expiref The expiref
 * @param string      $key     The session key
 * 
 * @return string
 */
function expiryNotificationTrigger(?string $expiref, string $key) : string {
   return $key;
}

$sessionParameters = $GLOBALS['TestingControl']['session'];

$compression = [
    'gzip' => [ 
        'file' => 'adodb-compress-gzip.php',
        'class' => 'ADODB_Compress_Gzip'
        ],
    'bzip2' => [
        'file' => 'adodb-compress-bzip2.php',
        'class' => 'ADODB_Compress_Bzip2'
    ]
];

$encryption = [
    'md5' => [
        'file' => 'adodb-encrypt-md5.php',
        'class' => 'ADODB_Encrypt_MD5'
        ],
    'sha1' => [
        'file' => 'adodb-encrypt-sha1.php',
        'class' => 'ADODB_Encrypt_SHA1'
        ],
        /*
        * Horde only works on unix based systems
        */
    'secret' => [
        'file' => 'adodb-encrypt-secret.php',
        'class' => 'ADODB_Encrypt_Secret'
        ],

];

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
    ADODB_Session::filter(new $compressionClass);
}
if ($encryptionClass) {
    ADODB_Session::filter(new $encryptionClass);
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
