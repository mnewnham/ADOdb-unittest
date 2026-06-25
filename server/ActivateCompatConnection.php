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
class ActivateCompatConnection {

    public function startup(bool $debug = false) {
        
        global $credentials;
        global $sessionParams;
        $persist = null;

        if (!$debug && isset($sessionParams['debug']) && $sessionParams['debug']){
            $debug = $sessionParams['debug'];
        }


        $clob = null;
        if ($credentials['driver'] == 'oci8') {
            //$clob = 'CLOB';
        }

        if (isset($sessionParams['clob']) && $sessionParams['clob']){
            $clob = $sessionParams['clob'];
        }

        
        if (isset($sessionParams['persist']) && $sessionParams['persist']){
            $persist = $sessionParams['persist'];
        }

        $options = [
            'table' => 'session_test',
            'persist' => $persist,
            'lob' => $clob,
            'debug' => $debug
        ];

        if (isset($credentials['parameters'])) {
            $options['parameters'] = $credentials['parameters'];
        }

        if (in_array($credentials['driver'], ['sqlite3', 'db2'] ) || substr($credentials['driver'],0,3 == 'pdo')) {
            $credentials['host'] = $credentials['dsn'];
        }

        $credentials['driver'] = str_replace('pdo-', 'pdo_', $credentials['driver']);

        ADOdb_Session::config(
            $credentials['driver'],
            $credentials['host'],
            $credentials['user'],
            $credentials['password'],
            $credentials['database'],
            $options
        );

        ADOdb_Session::encryptionKey('ALTERNATE KEY TEST');
        
        session_start(); 
    }
}