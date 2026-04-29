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
class DateFunctionsTest extends ADOdbTestCase
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
            "'$dbTs'"
        );

        $actualNowTime = strtotime($this->db->getOne($sql));


        $this->assertSame(
            $nowTime,
            $actualNowTime,
            'dbTimestamp should return a date that evaluates to the calculated timestamp'
        );

        print "
        ANT=[$actualNowTime]
        ";
        /*
        $now = sprintf("'%s'", $now);
        $this->assertSame(
            $now,
            $bts,
            'bindTimestamp should return a timestamp without quotes'
        );
        */
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


    /**
     * Test for {@see ADOConnection::sqlDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:sqldate
     *
     * @return void
     *
     */
    #[DataProvider('providerTestSqlDate')]
    public function testSqlDate(int $testMethod, string $format, ?int $timestamp): void
    {


        switch ($testMethod) {
            case 1:
                $expected = date($format, $timestamp);
                $timeString = date('Y-m-d H:i:s', $timestamp);
                $sql = "SELECT " . $this->db->sqlDate($format, $timeString);
                list($errno, $errmsg) = $this->assertADOdbError('sqlDate()');

                $actual = $this->db->getOne($sql);
                list($errno, $errmsg) = $this->assertADOdbError($sql);

                $message = 'sqlDate should return the portion of the ' .
                'provided timestamp identified by the format string: ' . $format;
                break;
            case 2:
                $expected = date($format);
                $sql = "SELECT " . $this->db->sqlDate($format);
                list($errno, $errmsg) = $this->assertADOdbError('sqlDate()');
                $actual = $this->db->getOne($sql);
                list($errno, $errmsg) = $this->assertADOdbError($sql);

                $message = 'sqlDate should return the portion of the ' .
                'current timestamp identified by the format string: ' . $format;
                break;
            case 3:
                $sql = "SELECT id,datetime_field 
                        FROM testtable_3 
                        WHERE datetime_field IS NOT NULL ";

                $this->db->storeFetchModes();
                $this->db->setFetchMode(ADODB_FETCH_NUM);

                $baseData = $this->db->getRow($sql);

                $this->db->restoreFetchModes();

                list($errno, $errmsg) = $this->assertADOdbError($sql);

                $baseData = array_values($baseData);

                $expected = date($format, strtotime($baseData[1]));

                $sql = sprintf(
                    "SELECT %s 
                   FROM testtable_3
                    WHERE id=%s",
                    $this->db->sqlDate($format, 'datetime_field'),
                    $baseData[0]
                );

                list($errno, $errmsg) = $this->assertADOdbError('sqlDate()');

                $actual = $this->db->getOne($sql);

                list($errno, $errmsg) = $this->assertADOdbError($sql);

                $message = 'sqlDate should return the portion of the ' .
                'date field identified by the format string: ' . $format;
                break;

            default:
                $this->fail("Invalid test method: $testMethod");
        }

        $message .= '. This may be caused by the difference in Time or Timezone of' .
        'the server if it is on a different machine than the client';



        $this->assertSame(
            "$expected",
            "$actual",
            $message
        );
    }

    /**
     * Data provider for testSqlDate
     *
     * @return array
     */
    public static function providerTestSqlDate(): array
    {
        $testPastTimestamp = strtotime('2000-01-02 03:04:05');
        $testNowTimestamp = time();

        return [
            /*
            [1, 'Y', $testPastTimestamp],
            [1, 'm', $testPastTimestamp],
            [1, 'M', $testPastTimestamp],
            [1, 'd', $testPastTimestamp],
            [1, 'H', $testPastTimestamp],
            [1, 'i', $testPastTimestamp],
            [1, 's', $testPastTimestamp],
            */
            [2, 'Y', $testNowTimestamp],
            [2, 'm', $testNowTimestamp],
            [2, 'M', $testNowTimestamp],
            [2, 'd', $testNowTimestamp],
            [2, 'H', $testNowTimestamp],
            [2, 'i', $testNowTimestamp],
            [2, 's', $testNowTimestamp],

            [3, 'Y', null],
            [3, 'm', null],
            [3, 'M', null],
            [3, 'd', null],
            [3, 'H', null],
            [3, 'i', null],
            [3, 's', null],
        ];
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

        $sql = "SELECT " . $this->db->unixDate($now);
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

    /**
     * Test for {@see ADOConnection::offsetDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffsetDate(): void
    {

        $offset = 7;
        $nowStamp = date('Y-m-d', strtotime('today +7 days'));

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $this->db->offsetDate($offset)
        );

        list($errno, $errmsg) = $this->assertADOdbError('offsetDate()');

        $od = $this->db->getOne($sql);

        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $nowStamp,
            $od,
            'Offset date should return the date 1 week in the future'
        );

        $offset = -7;
        $nowStamp = date('Y-m-d', strtotime('today -7 days'));

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $this->db->offsetDate($offset)
        );



        list($errno, $errmsg) = $this->assertADOdbError('offsetDate()');
        $od = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $nowStamp,
            $od,
            'Offset date should return the date 1 week in the past'
        );

        /*
        * Test using a timestamp basedate to test the effect
        * of the time of day and a fractional offset
        */
        $offset = 1.5; // 12 hours

        $nowStamp = date('Y-m-d', strtotime('now + 36 hours'));

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $this->db->offsetDate($offset, date('Y-m-d H:i'))
        );

        list($errno, $errmsg) = $this->assertADOdbError('offsetDate()');
        $od = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $nowStamp,
            $od,
            'Offset date using hours should return the date 12 hours ' .
            'from now based on the current time of day'
        );

        /*
        * Test using a column as the base date
        */
        $sql = "SELECT date_field 
                  FROM testtable_3 
                 WHERE number_run_field=9";

        $dateField = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $nowStamp = date('Y-m-d', strtotime($dateField . ' + 7 days'));

        $offset = 7; // 1 week
        $sql = "SELECT {$this->db->offsetDate($offset, 'date_field')}
                  FROM testtable_3 
                 WHERE number_run_field=9";
        list($errno, $errmsg) = $this->assertADOdbError('offsetDate()');
        $od = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $nowStamp,
            $od,
            'Offset date using a column as the base date should ' .
            'return the date 1 week in the future based on the date_field column'
        );
    }

    /**
     * Test for {@see ADOConnection::offsetDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffsetDateUsingHours(): void
    {

        $offset = 7;
        $nowStamp = date('Y-m-d', strtotime('today +7 days'));

        $offsetHours = sprintf('%d/24', $offset * 24); // Convert days to hours

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $this->db->offsetDate($offsetHours)
        );

        list($errno, $errmsg) = $this->assertADOdbError('offsetDate()');
        $od = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);


        $this->assertSame(
            $nowStamp,
            $od,
            'Offset date using hours should return the date 1 week in the future'
        );

        $offset = -7;
        $nowStamp = date('Y-m-d', strtotime('today -7 days'));

        $offsetHours = sprintf('%s/24', $offset * 24); // Convert days to hours

        $sql = "SELECT " . $this->db->offsetDate($offsetHours);
        list($errno, $errmsg) = $this->assertADOdbError('offsetDate()');
        $od = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $nowStamp,
            $od,
            'Offset date using hours should return the date 1 week in the past'
        );
    }
}
