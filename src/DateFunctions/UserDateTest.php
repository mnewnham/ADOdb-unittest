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
class UserDateTest extends DateHandling
{
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

       // print "CALL UD WITH $time\n";
        $userDate = $this->db->userDate($time, 'Y-m-d');

        //print "UD =$userDate\n"; 

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

}
