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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class NewSessionTest
 *
 * Test cases for for ADOdb Session Services
 */
class ExistingSessionTest extends ADOdbTestCase
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

        parent::setUpBeforeClass();
    }

    public function setup(): void
    {
        if ($GLOBALS['skipSessionTests'] == 1) {
            $this->markTestSkipped('Session testing is disabled');
            return;
        }

         if ($GLOBALS['ADOdbConnection']->database == ':memory:') {
            $this->markTestSkipped('Sessions cannot be tested using a memory based connection');
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
    public function testUseExistingSession(): void
    {
        $reflection = new \ReflectionClass($this);
        $class = $reflection->getShortName();

        list ($a, $b) = $this->transmitSessionTest(
            __FILE__,
            $class,
            'testUseExistingSession',
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
            'Call to server should return a json encoded object'
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
            'Call to server should return a json encoded object'
        );

        $this->assertEquals(
            11,
            $idObject->session->integer_field,
            'Session should have incremented integer_field value to 11'
        );
    }

    /**
     * Write the CLOB test item into the session
     *
     * @return void
     */
    public function testWriteClobIntoSession(): void
    {
        $reflection = new \ReflectionClass($this);
        $class = $reflection->getShortName();

        list ($a, $b) = $this->transmitSessionTest(
            __FILE__,
            $class,
            'testWriteClobIntoSession',
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


    /**
     * Write the CLOB test item into the session
     *
     * @return void
     */
    public function testReadClobFromSession(): void
    {
        $reflection = new \ReflectionClass($this);
        $class = $reflection->getShortName();

        list ($a, $b) = $this->transmitSessionTest(
            __FILE__,
            $class,
            'testReadClobFromSession',
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

        if (isset($idObject->error)) {
            $this->fail(
                $idObject->error
            );
        }

        $this->assertFileExists(
            $idObject->newFileName,
            'The clob file should have been written to ' . $idObject->newFileName
        );

        /*
        * Do some filesystem checks
        */
        $originalFileSize = filesize($idObject->originalFileName);
        $newFileSize      = filesize($idObject->newFileName);

        $this->assertSame(
            $originalFileSize,
            $newFileSize,
            'Clob file size after storage in session should match the original file size'
        );
    }
}
