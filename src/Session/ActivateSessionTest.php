<?php

/**
 * Tests cases for Session functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\Session;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class NewSessionTest
 *
 * Test cases for for ADOdb Session Services
 */
class ActivateSessionTest extends ADOdbTestCase
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        if ($GLOBALS['skipSessionTests'] == 1) {
            return;
        }
        $db = $GLOBALS['ADOdbConnection'];
        /*
        * Load the table to test data length tests
        */
        $schemaFile = sprintf(
            '%s/DatabaseSetup/%s/sessions-schema.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->startTrans();
        }

        $db->execute('DROP TABLE IF EXISTS session_test');

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->completeTrans();
        }


        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->startTrans();
        }

        $ok = readSqlIntoDatabase($db, $schemaFile);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->completeTrans();
        }

        parent::setUpBeforeClass();
    }


    public function setup(): void
    {
        if ($GLOBALS['skipSessionTests'] == 1) {
            $this->markTestSkipped('Session testing is disabled');
            return;
        }
        parent::setup();
    }


    /**
     * Test to initialize a new session. We do this so that the session id is constant
     * through thr test, curl will generate a new one with every connection unless
     * we provide the session as a cookie
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:addcolumnsql
     *
     * @return void
     */
    public function testInitializeNewSession(): void
    {
        $reflection = new \ReflectionClass($this);
        $class = $reflection->getShortName();

        list ($a, $b) = $this->transmitSessionTest(
            __FILE__,
            $class,
            'testInitializeNewSession',
            '',
            [
                'Cookie: PHPSESSID=' . $GLOBALS['unittest-id']
            ]
        );

        $this->assertSame(
            200,
            $a,
            'Call to server should return 200 OK'
        );

        $idObject = json_decode($b);

        $this->assertIsObject(
            $idObject,
            sprintf(
                'Call to server should return a json encoded object, returned %s',
                $b
            )
        );

        $GLOBALS['unittest-id'] = $idObject->id;
    }

    public function testReadSession(): void
    {
        $reflection = new \ReflectionClass($this);
        $class = $reflection->getShortName();

        list ($a, $b) = $this->transmitSessionTest(
            __FILE__,
            $class,
            'testReadSession',
            '',
            [
                'Cookie: PHPSESSID=' . $GLOBALS['unittest-id']
            ]
        );

        $idObject = json_decode($b);

        $this->assertIsObject(
            $idObject,
            sprintf(
                'Call to server should return a json encoded object, returned %s',
                $b
            )
        );

        $this->assertEquals(
            2,
            $idObject->session->integer_field,
            'Session should have incremented integer_field value from 1 to 2'
        );
    }
}
