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
 * Class ExistingSessionTest
 *
 * Test cases for for ADOdb Session Services
 */
class ShutdownSessionTest
{
    /**
     * Test
     *
     * @return void
     */
    public function testDestroySession(): void
    {

        $acc = new ActivateCompatConnection();
        $acc->startup();

        $id = session_id();
        
        ADOdb_Session::destroy($id);
        
        $c = new \stdClass();
        $c->id = $id;
        $c->test = 'testDestroySession';
        print json_encode($c);
    }
}
