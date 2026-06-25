<?php
/**
 * Response Tests cases for Session functions of ADODb
 * This tests retrieving all the current parameters
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

/**
 * Class SessionParametersTest
 *
 * Test cases for for ADOdb Session Services
 */
class SessionParametersTest
{
    /**
     * Test The parameters set into the session connection
     *
     * @return void
     */
    public function testFetchParameters(): void
    {

        $acc = new ActivateCompatConnection();
        $acc->startup();
        
        $cls = new \stdClass();
        $cls->id = session_id();
        $cls->session = $_SESSION;
        $cls->test    = 'testFetchParameters';
        $cls->staticData = [
            'table' => ADOdb_Session::table(),
            'driver' => ADOdb_Session::driver(),
            'host' => ADOdb_Session::host(),
            'user' => ADOdb_Session::user(),
            'password' => ADOdb_Session::password(),
            'database' => ADOdb_Session::database(),
            'lifetime' => ADOdb_Session::lifetime(),
            'persist' => ADOdb_Session::persist(),
            'parameters' => ADOdb_Session::parameters(),
            'clob' => ADOdb_Session::clob(),
            'encryption_key' => ADOdb_Session::encryptionKey()
            
        ];

        $cls ->objectData = [
            'table' => $GLOBALS['ADODB_SESSION_OBJECT']->table(),
            'driver' => $GLOBALS['ADODB_SESSION_OBJECT']->driver(),
            'host' => $GLOBALS['ADODB_SESSION_OBJECT']->host(),
            'user' => $GLOBALS['ADODB_SESSION_OBJECT']->user(),
            'password' => $GLOBALS['ADODB_SESSION_OBJECT']->password(),
            'database' => $GLOBALS['ADODB_SESSION_OBJECT']->database(),
            'lifetime' => $GLOBALS['ADODB_SESSION_OBJECT']->lifetime(),
            'persist' => $GLOBALS['ADODB_SESSION_OBJECT']->persist(),
            'parameters' => $GLOBALS['ADODB_SESSION_OBJECT']->parameters(),
            'clob' => $GLOBALS['ADODB_SESSION_OBJECT']->clob(),
            'encryption_key' => $GLOBALS['ADODB_SESSION_OBJECT']->encryptionKey()
        ];

        print json_encode($cls);

    }
}