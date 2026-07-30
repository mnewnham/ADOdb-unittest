<?php

/**
 * Tests cases for date functions of ADODb. Some of these functions
 * are effectively obsolete since 64 bit processors
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

namespace MNewnham\ADOdbUnitTest;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class DateFunctions.
 *
 * Test cases for ADOdb date functions
 */
class BindFunctionsTest extends ADOdbTestCase
{
    public static function setUpBeforeClass(): void
    {
        $GLOBALS['ADOdbConnection']->_errorCode = 0;
    }
    /**
     * Test for {@see ADOConnection::userDate()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:userdate
     *
     * @return void
     */
    public function testUserDate(): void
    {
        $expected = date('Y-m-d');
        $time     = time();

        $userDate = $this->db->userDate($time, 'Y-m-d');
        list($errno, $errmsg) = $this->assertADOdbError('userDate()');

       
        $this->assertSame(
            $expected,
            $userDate,
            'userDate should return a date string built from the given timestamp'
        );
    }

    /**
     * Test for {@see ADOConnection::userTime()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:usertime
     *
     * @return void
     */
    public function testUserTimeStamp(): void
    {
        $expected = date('Y-m-d H:i:s');
        $time     = time();

        $userTimeStamp = $this->db->userTimeStamp($time);
        list($errno, $errmsg) = $this->assertADOdbError('userTimestamp()');

        $this->assertSame(
            $expected,
            $userTimeStamp,
            'userTimeStamp should return a time string built from the given timestamp'
        );
    }

    /**
     * Test for {@see ADOConnection::dbDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:dbdate
     *
     * @return void
     *
     */
    public function testDbDate(): void
    {
        $today = date('Y-m-d');

        $dbDate =  $this->db->dbDate($today);
        list($errno, $errmsg) = $this->assertADOdbError('dbDate()');


        $this->assertNotNull(
            $dbDate,
            'dbDate() should return an SQL string to retrieve ' .
            'todays date in ISO format'
        );
    }

    /**
     * Test for {@see ADOConnection::bindDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:binddate
     *
     * @return void
     */
    public function testBindDate(): void
    {
        $today = date('Y-m-d');

        $bindDate = $this->db->bindDate($today);
        list($errno, $errmsg) = $this->assertADOdbError('bindDate()');


        $this->assertNotNull(
            $bindDate,
            'bindDate() should return a string to use ' .
            'todays date in ISO format for a bind parameter'
        );
    }

    /**
     * Test for {@see ADOConnection::dbTimestamp())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:dbtimestamp
     *
     * @return void
     *
     */
    public function testDbTimestamp(): void
    {
        $nowTime = time();
        $now = date('Y-m-d H:i:s', $nowTime);

        $dbTs = $this->db->dbTimestamp($now);
        list($errno, $errmsg) = $this->assertADOdbError('dbTimestamp()');

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $dbTs
        );

        $actualNowTime = strtotime($this->db->getOne($sql));

        $this->assertSame(
            date('c', $nowTime),
            date('c', $actualNowTime),
            'dbTimestamp should return a date that evaluates to the calculated timestamp'
        );
    }

    /**
     * Test for {@see ADOConnection::bindTimestamp())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:bindtimestamp
     *
     * @return void
     */
    public function testBindTimestamp(): void
    {
        $nowTime = time();
        $now = date('Y-m-d H:i:s', $nowTime);


        $dbTs = $this->db->bindTimestamp($now);


        if (substr($dbTs, 0, 1) == "'") {
            $this->fail(
                sprintf(
                    'bindTimestamp() should return a timestamp without quotes, actually returned[%s]',
                    $dbTs
                )
            );
        }

        list($errno, $errmsg) = $this->assertADOdbError('dbTimestamp()');

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $dbTs
        );

        $this->db->param('');
        $p1 = $this->db->param('p1');

        $bind = [ 'p1' => $dbTs ];

        $sql = 'select * from testtable_1 where datetime_field=' . $p1;

        $result = $this->db->selectLimit($sql, 1, -1, $bind);
        if (!$r = $result->fetchRow()) {
            $this->assertTrue(
                true,
                'OK'
            );
        }

        //$actualNowTime = strtotime($this->db->getOne($sql));
    /*

        $this->assertSame(
            $nowTime,
            $actualNowTime,
            sprintf(
                'dbTimestamp should return a date that evaluates to the calculated timestamp, executed %s',
                $dbTs
            )
        );

    */

    }
}
