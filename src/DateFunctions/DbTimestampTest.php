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

namespace MNewnham\ADOdbUnitTest\DateFunctions;

use MNewnham\ADOdbUnitTest\DateFunctions\DateHandling;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class DateFunctions.
 *
 * Test cases for ADOdb date functions
 */
class DbTimestampTest extends DateHandling
{

    /**
     * Test for {@see ADOConnection::dbTimestamp())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:dbtimestamp
     *
     * @return void
     *
     */
    public function testDbTimestampWithDate(): void
    {
        //print "\n------------------ start DBTS --------------------\n";
        $nowTime = time();
        $now = date('Y-m-d', $nowTime);

        $dbTs = $this->db->dbTimestamp($now);
        //list($errno, $errmsg) = $this->assertADOdbError('dbTimestamp()');

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $dbTs
        );

        //print "DBTS EXEC $sql\n";
  
        $actualNowTime = strtotime($this->db->getOne($sql));

        $this->assertSame(
            date('Y-m-d', $nowTime),
            date('Y-m-d', $actualNowTime),
            'dbTimestamp should return a date portion that evaluates to the calculated timestamp'
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
    public function testDbTimestampWithTime(): void
    {
        $nowTime = time();
        $now = date('Y-m-d H:i:s', $nowTime);

        $dbTs = $this->db->dbTimestamp($nowTime);

        list($errno, $errmsg) = $this->assertADOdbError('dbTimestamp()');

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $dbTs
        );

        $stringTime = $this->db->getOne($sql);

        $actualNowTime = strtotime($stringTime);

        $this->assertSame(
            date('c', $nowTime),
            date('c', $actualNowTime),
            'dbTimestamp should return a date that evaluates to the calculated timestamp'
        );
    }
}
