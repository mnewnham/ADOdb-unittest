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

namespace MNewnham\ADOdbUnitTest\Session;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class SessionParametersTest
 *
 * Test cases for for ADOdb Session Services
 */
class SessionParametersTest extends ADOdbTestCase
{
    /**
     * Test
     *
     * @return void
     */
    public function testFetchParameters(): void
    {

        $sessionParams = $GLOBALS['TestingControl']['session'];

        $persist = null;

        if (isset($sessionParams['persist']) && $sessionParams['persist']) {
            $persist = $sessionParams['persist'];
        }

        $clob = null;
        if ($GLOBALS['credentials']['driver'] == 'oci8') {
            //$clob = 'CLOB';
        }

        if (isset($sessionParams['clob']) && $sessionParams['clob']) {
            $clob = $sessionParams['clob'];
        }

        $testItems = array_merge(
            $GLOBALS['credentials'],
            [
                'table' => 'session_test',
                'lifetime' => 1440,
                'encryption_key' => 'ALTERNATE KEY TEST',
                'persist' => $persist,
                'parameters' => new \stdClass(),
                'clob' => $clob
            ]
        );

        if (isset($GLOBALS['credentials']['dsn']) &&  $GLOBALS['credentials']['dsn']) {
            $testItems['host'] =  $GLOBALS['credentials']['dsn'];
        }


        $reflection = new \ReflectionClass($this);
        $class = $reflection->getShortName();

        list ($a, $b) = $this->transmitSessionTest(
            __FILE__,
            $class,
            'testFetchParameters',
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

        foreach (['table', 'driver','host', 'user', 'password', 'database', 'lifetime', 'persist', 'parameters', 'clob', 'encryption_key'] as $cred) {
            $this->assertEquals(
                $testItems[$cred],
                $idObject->staticData->$cred,
                sprintf(
                    'Compatibility Session parameter [%s] should equal the credentials value',
                    $cred
                )
            );

            $this->assertEquals(
                $testItems[$cred],
                $idObject->objectData->$cred,
                sprintf(
                    '8.5 Object Session parameter [%s] should equal the credentials value',
                    $cred
                )
            );
        }
    }
}
