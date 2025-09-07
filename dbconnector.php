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
 * @copyright 2025 Mark Newnham, Damien Regad and the ADOdb community
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 * 
 * @link https://github.com/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

require __DIR__ . '/ADOdbTestCase.php';


use PHPUnit\Framework\TestCase;

/**
 * Reads an external SQL file and executes its statements against the database.
 * The format of the SQL file must match the database's SQL dialect.
 * 
 * @param object $db       The database connection object.
 * @param string $fileName The name of the SQL file to read.
 * 
 * @return void
 *
 * @example DatabaseSetup/db2/table-schema.sql 
*/
function readSqlIntoDatabase(object $db, string $fileName) : void
{
    
    if (!file_exists($fileName)) {
        die('Schema ' . $fileName . ' file for unit testing not found');
    }
    $filePointer = fopen($fileName, 'r');
    $executionPoint = '';
    while ($filePointer && !feof($filePointer)) {
        $line = fgets($filePointer);
        $line = trim($line);
        
        if (!$line) {
            continue; // skip empty lines
        }
        
        if (substr($line, 0, 2) === '--') {
            continue; // skip comments
        }   

        $executionPoint .= $line;
        if (preg_match('/;$/', $line)) {
            $executionPoint = trim($executionPoint, ';');
            if ($executionPoint) {
                $db->execute($executionPoint);
            }
            $executionPoint = ''; // reset for the next statement
            continue; 
        }
        $executionPoint .= ' ';
        
    }
    fclose($filePointer);
}


$iniFile = stream_resolve_include_path('adodb-unittest.ini');

if (!$iniFile) {
    die('could not find adodb-unittest.ini in the PHP include_path');
}

$availableCredentials = parse_ini_file($iniFile, true);

if (!array_key_exists('ADOdb', $availableCredentials)) {
    /* 
    * If the ADOdb section is not present, we assume the directory is the 
    * parent of the current directory
    */
    $availableCredentials['ADOdb'] = array(
        'directory' => dirname(__DIR__),
        'casing' => 1, // 1= Upper Case
        
    );
}

$ADOdbSettings        = $availableCredentials['ADOdb'];
if (!array_key_exists('casing', $ADOdbSettings)) {
    $ADOdbSettings['casing'] = 1; // 1= Upper Case
    $availableCredentials['ADOdb']['casing'] = 1;
}

if (!array_key_exists('blob', $availableCredentials)) {
    die('blob section not found in adodb-unittest.ini. See the documentation for details on how to set this up');
}

require_once $ADOdbSettings['directory'] . '/adodb.inc.php';
require_once $ADOdbSettings['directory'] . '/adodb-xmlschema03.inc.php';

global $argv;
global $db;

$adoDriver = '';

define('ADODB_ASSOC_CASE', $ADOdbSettings['casing']);


/*
* First try to use the active flag in the ini file because
* Version 12 of PHPUnit does not support the unnammed parameters
*/
foreach ($availableCredentials as $driver=>$driverOptions) {
    if (isset($driverOptions['active']) && $driverOptions['active']) {
        $adoDriver = $driver;
        break;
    }
}

if (!$adoDriver) {
 
    $o = (preg_grep('/dbconnector/', $argv));

    if ($o) {
        //die('unit tests must contain either an entry in the INI file or a dbconnector argument');
    //}

    /*
    * See if there is an unnamed parameter
    */
    $o = array_keys($o);
    $oIndex = $o[0] + 1;

    //if (!array_key_exists($oIndex, $argv)) {
      //  die('The dbconnector argument must be followed by the name of the driver');
    //}
    /*
    * Match the location of the bootstrap load
    * the driver name is the next argument
    */

    $adoDriver = strtolower($argv[$oIndex] ?? '');

    unset($argv[$oIndex]);
    }

}

/*
* At the point we either have a driver via the active flog or the command line
*/

if (!isset($availableCredentials[$adoDriver])) {
    die('login credentials not available for driver ' . $adoDriver); 
}

/*
* Push global settings into the ini file
*/
$iniParams = $availableCredentials['globals'];
if (is_array($iniParams)) {
    foreach ($iniParams as $key => $value) {
                
        ini_set($key, $value);
    }
}


$template = array(
    'dsn'=>'',
    'host'=>null,
    'user'=>null,
    'password'=>null,
    'database'=>null,
    'parameters'=>null,
    'debug'=>0
);


$credentials = array_merge(
    $template, 
    $availableCredentials[$adoDriver]
);

$loadDriver = str_replace('pdo-', 'PDO\\', $adoDriver);

$db = newAdoConnection($loadDriver);
$db->debug = $credentials['debug'];

if ($credentials['parameters']) {

    $p = explode(';', $credentials['parameters']);
    $p = array_filter($p);
    foreach ($p as $param) {
        $scp = explode('=', $param);
        if (preg_match('/^[0-9]+$/', $scp[0]))
            $scp[0] = (int)$scp[0];
        if (preg_match('/^[0-9]+$/', $scp[1]))
            $scp[1] = (int)$scp[1];
        
        $db->setConnectionParameter($scp[0], $scp[1]);
    }
}

if ($credentials['dsn']) {
    $db->connect(
        $credentials['dsn'],
        $credentials['user'],
        $credentials['password'],
        $credentials['database']
    );
} else {
    $db->connect(
        $credentials['host'],
        $credentials['user'],
        $credentials['password'],
        $credentials['database']
    );
}

if (!$db->isConnected()) {
    die(sprintf('%s database connection not established', $adoDriver));
}

/*
* This is now available to unittests. The caching section will need this info
*/
$GLOBALS['ADOdbConnection'] = &$db;
$GLOBALS['ADOdriver']       = $adoDriver;
$GLOBALS['loadDriver']      = $loadDriver;
$GLOBALS['ADOxmlSchema']    = false;
$GLOBALS['TestingControl']  = $availableCredentials;
$GLOBALS['globalTransOff']  = 0;

//$db->startTrans();

$tableSchema = sprintf(
    '%s/DatabaseSetup/%s/table-schema.sql', 
    dirname(__FILE__), 
    $adoDriver
);

/*
* Loads the schema based on the DB type
*/

readSqlIntoDatabase($db, $tableSchema);


/*
* Reads common format data and nserts it into the database
*/
$table3Data = sprintf('%s/DatabaseSetup/table3-data.sql', dirname(__FILE__));

readSqlIntoDatabase($db, $table3Data);



/*
* Set up the data dictionary
*/
$GLOBALS['ADOdataDictionary'] = NewDataDictionary($db);

$ADODB_CACHE_DIR = '';
if (array_key_exists('caching', $availableCredentials)) {   

    $cacheParams = $availableCredentials['caching'];
    switch ($cacheParams['cacheMethod'] ?? 0) {
    case 1:
        $ADODB_CACHE_DIR = $cacheParams['cacheDir'] ?? '';
        break;
    case 2:
        $db->memCache     = true;
        $db->memCacheHost = $cacheParams['cacheHost'];
        $db->memCachePort = 11211;
        break;
    }
}

/**
 * Set some global variables for the tests
 */
$ADODB_QUOTE_FIELDNAMES = false;
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$ADODB_GETONE_EOF = null;
$ADODB_COUNTRECS = true;