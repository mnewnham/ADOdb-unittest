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
class UnixFunctionsTest extends ADOdbTestCase
{
    public static function setUpBeforeClass(): void
    {
        $GLOBALS['ADOdbConnection']->_errorCode = 0;
    }
    
    /**
     * Test for {@see ADOConnection::unixDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:unixdate
     *
     * @return void
     */
    public function testUnixDate(): void
    {
        $now = time();

         $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $this->db->unixDate($now)
        );

        list($errno, $errmsg) = $this->assertADOdbError('unixDate()');

        $unixDate = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            $now,
            $unixDate,
            'UnixDate() should return a string time in the default format'
        );
    }

    /**
     * Test for {@see ADOConnection::unixTimestamp())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:unixtimestamp
     *
     * @return void
     */
    public function testUnixTimestamp(): void
    {

        $now      = time();
        $nowStamp = date('Y-m-d H:i:s', $now);

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $this->db->unixTimestamp($nowStamp)
        );

        list($errno, $errmsg) = $this->assertADOdbError('unixTimestamp()');

        $unixTs = (int)$this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $now,
            $unixTs,
            'unixTimestamp() should return a UNIX timestamp from ' .
            'the passed date string'
        );
    }
}
