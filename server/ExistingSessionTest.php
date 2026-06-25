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
class ExistingSessionTest
{
    /**
     * Test
     *
     * @return void
     */
    public function testUseExistingSession(): void
    {

        $acc = new ActivateCompatConnection();
        $acc->startup();
        
        $_SESSION['integer_field'] = 10;

        $c = new \stdClass();
        $c->id = session_id();
        $c->test = 'testUseExistingSession';
        print json_encode($c);
    }

    /**
     * Test adding an integer value to an existing field
     *
     * @return void
     */
    public function testReadSession(): void
    {

        $acc = new ActivateCompatConnection();
        $acc->startup();

        $_SESSION['integer_field']++;

        $cls = new \stdClass();
        $cls->id = session_id();
        $cls->session = $_SESSION;
        $cls->test    = 'testReadSession';


        print json_encode($cls);
    }

    /**
     * Test writing large text data into a session record
     *
     * @return void
     */
    public function testWriteClobIntoSession(): void
    {

        $acc = new ActivateCompatConnection();
        $acc->startup();

        if (!isset($GLOBALS['TestingControl']['blob'])) {
           
            $cls = new \stdClass();
            $cls->id = session_id();
            $cls->session = $_SESSION;
            $cls->test    = 'testWriteClobIntoSession';
            $cls->error   = 'No CLOB test data available';

            print json_encode($cls);
            return;
        }

        $blobSection = $GLOBALS['TestingControl']['blob'];

        if (!isset($blobSection['testClob'])) {
            $cls = new \stdClass();
            $cls->id = session_id();
            $cls->session = $_SESSION;
            $cls->test    = 'testWriteClobIntoSession';
            $cls->error   = 'No CLOB test data available';

            print json_encode($cls);
            return;
        }

        $testData = file_get_contents($blobSection['testClob']);

        $_SESSION['big_data'] = $testData;

        $cls = new \stdClass();
        $cls->id = session_id();
        $cls->session = $_SESSION;
        $cls->test    = 'testWriteClobIntoSession';


        print json_encode($cls);
    }

    /**
     * Test writing large text data into a session record
     *
     * @return void
     */
    public function testReadClobFromSession(): void
    {

        $acc = new ActivateCompatConnection();
        $acc->startup();

        if (!isset($GLOBALS['TestingControl']['blob'])) {
            $cls = new \stdClass();
            $cls->id = session_id();
            $cls->session = $_SESSION;
            $cls->test    = 'testWriteClobIntoSession';
            $cls->error   = 'No CLOB test data available';

            print json_encode($cls);
            return;
        }

        $blobSection = $GLOBALS['TestingControl']['blob'];

        if (!isset($blobSection['testClob'])) {
            $cls = new \stdClass();
            $cls->id = session_id();
            $cls->session = $_SESSION;
            $cls->test    = 'testWriteClobIntoSession';
            $cls->error   = 'No CLOB test data available';

            print json_encode($cls);
            return;
        }

        if (!isset($_SESSION['big_data'])) {
            $cls = new \stdClass();
            $cls->id = session_id();
            $cls->session = $_SESSION;
            $cls->test    = 'testWriteClobIntoSession';
            $cls->error   = 'CLOB Test data was not written to session';

            print json_encode($cls);
            return;
        }

        $clobName = $GLOBALS['TestingControl']['blob']['testClob'];

        $newFileArray = explode('.', $clobName);
        $extension = array_pop($newFileArray);
        $newFile = implode('.', $newFileArray) . '-decoded.' . $extension;

        
        file_put_contents(
            $newFile,
            $_SESSION['big_data']
        );

        $cls = new \stdClass();
        $cls->id = session_id();
        $cls->session = $_SESSION;
        $cls->test    = 'testReadClobFromSession';
        $cls->originalFileName = $clobName;
        $cls->newFileName      = $newFile;

        print json_encode($cls);

    }
}
