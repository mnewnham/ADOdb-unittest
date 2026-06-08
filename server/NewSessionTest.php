<?php

/**
 * Response Tests cases for Session functions of ADODb
 * This tests establishing a new connection
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
 * Class NewSessionTest
 *
 * Test cases for for ADOdb Session Services
 */
class NewSessionTest
{
    /**
     * Test
     *
     * @return void
     */
    public function testInitializeNewSession(): void
    {

        global $credentials;

        $options = [
            'table' => 'session_test'
        ];

        /*
        * Set a timeout of 1 hour
        */
        $timeoutMinutes = 60;
        $timeoutSeconds = $timeoutMinutes * 60;

        ADOdb_Session::config(
            $credentials['driver'],
            $credentials['host'],
            $credentials['user'],
            $credentials['password'],
            $credentials['database'],
            $options
        );

        if ($timeoutMinutes > 0) {
            ADODB_Session::lifetime($timeoutSeconds);
        }
        //ADODB_Session::debug(true);
        session_start();

        $_SESSION['integer_field'] = 1;

        $c = new \stdClass();
        $c->id = session_id();
        $c->test = 'testInitializeNewSession';
        print json_encode($c);
    }

    /**
     * Test
     *
     * @return void
     */
    public function testReadSession(): void
    {

        global $credentials;

        $options = [
            'table' => 'session_test'
        ];

        /*
        * Set a timeout of 1 hour
        */
        $timeoutMinutes = 60;
        $timeoutSeconds = $timeoutMinutes * 60;

        ADOdb_Session::config(
            $credentials['driver'],
            $credentials['host'],
            $credentials['user'],
            $credentials['password'],
            $credentials['database'],
            $options
        );

        session_start();

        $_SESSION['integer_field']++;

        $cls = new \stdClass();
        $cls->session = $_SESSION;
        $cls->test    = 'testReadSession';


        print json_encode($cls);
    }
}
