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

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class DateFunctions.
 *
 * Test cases for ADOdb date functions
 */
class DatePeriodExtractionTest extends ADOdbTestCase
{
    public static function setUpBeforeClass(): void
    {
        $GLOBALS['ADOdbConnection']->_errorCode = 0;
    }

    /**
     * Test for {@see ADOConnection::year())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:year
     *
     * @return void
     */
    public function testYearFromDateField(): void
    {
        /*
        * Retrieve a record with a known year
        */
        $sql = "SELECT {$this->db->year('date_field')} 
                  FROM testtable_3 
                 WHERE number_run_field=9";

        $testResult     = (string)$this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $expectedResult = '1959';

        $this->assertSame(
            $expectedResult,
            $testResult,
            'Expected year portion of date_field to be 1959'
        );
    }

    /**
     * Test for {@see ADOConnection::month()
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:month
     *
     * @return void
     */
    public function testMonthFromDateField(): void
    {
        /*
        * Retrieve a record with a known month
        */
        $sql = "SELECT {$this->db->month('date_field')}
                  FROM testtable_3 
                 WHERE number_run_field=9";

        $testResult     = (string)$this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);


        $expectedResult = '08';

        $this->assertSame(
            $expectedResult,
            $testResult,
            'Test of month portion of date_field should be 08'
        );
    }

    /**
     * Test for {@see ADOConnection::day())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:day
     *
     * @return void
     */
    public function testDayFromDateField(): void
    {

        /*
        * Retrieve a record with a known day
        */
        $sql = "SELECT {$this->db->day('date_field')} 
                  FROM testtable_3 
                 WHERE number_run_field=9";


        $testResult     = (string)$this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $expectedResult = '29';

        $this->assertSame(
            $testResult,
            $expectedResult,
            'Test of day portion of date_field should be 29'
        );
    }

    /**
     * Test for {@see ADOConnection::year())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:year
     *
     * @return void
     */
    public function testYearFromDateTimeField(): void
    {
        /*
        * Retrieve a record with a known year
        */
        $sql = "SELECT {$this->db->year('datetime_field')} 
                  FROM testtable_3 
                 WHERE number_run_field=9";

        $testResult     = (string)$this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $expectedResult = '1959';

        $this->assertSame(
            $expectedResult,
            $testResult,
            'Expected year portion of datetime_field to be 1959'
        );
    }

    /**
     * Test for {@see ADOConnection::month()
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:month
     *
     * @return void
     */
    public function testMonthFromDateTimeField(): void
    {
        /*
        * Retrieve a record with a known month
        */
        $sql = "SELECT {$this->db->month('datetime_field')}
                  FROM testtable_3 
                 WHERE number_run_field=9";

        $testResult     = (string)$this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);


        $expectedResult = '08';

        $this->assertSame(
            $expectedResult,
            $testResult,
            'Test of month portion of datetime_field should be 08'
        );
    }

    /**
     * Test for {@see ADOConnection::day())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:day
     *
     * @return void
     */
    public function testDayFromDateTimeField(): void
    {

        /*
        * Retrieve a record with a known day
        */
        $sql = "SELECT {$this->db->day('datetime_field')} 
                  FROM testtable_3 
                 WHERE number_run_field=9";


        $testResult     = (string)$this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $expectedResult = '29';

        $this->assertSame(
            $testResult,
            $expectedResult,
            'Test of day portion of datetime_field should be 29'
        );
    }

}
