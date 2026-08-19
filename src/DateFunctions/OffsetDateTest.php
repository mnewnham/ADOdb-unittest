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
 * Class OffsetDateTest
 *
 * Test cases for ADOdb offset date functions
 */
class OffsetDateTest extends ADOdbTestCase
{
    public static function setUpBeforeClass(): void
    {
        $GLOBALS['ADOdbConnection']->_errorCode = 0;
    }

    /**
     * Test for {@see ADOConnection::offsetDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffsetPlusOneWeek(): void {

        $offset   = 7;
        $nowStamp = date('Y-m-d', strtotime('now +7 days'));

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
    }

    /**
     * Test for {@see ADOConnection::offsetDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffsetLessOneWeek(): void {

        $offset = -7;
        $nowStamp = date('Y-m-d', strtotime('now -7 days'));

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

    }

    /**
     * Test for {@see ADOConnection::offsetDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffset90minutes(): void {

        /*
        * Test using a timestamp basedate to test the effect
        * of the time of day and a fractional offset
        */
        $offset = 1.5 / 24; // 12 hour s

        $nowStamp = date('Y-m-d H:i', strtotime('now + 90 minutes'));

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
            'Offset date using hours should return the date 90 minute ' .
            'from now based on the current time of day'
        );

    }

    /**
     * Test for {@see ADOConnection::offsetDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffsetUsingDateField(): void
    {

        /*
        * Test using a column as the base date
        */
        $sql = "SELECT date_field 
                  FROM testtable_3 
                 WHERE number_run_field=9";

        $dateField = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $nowStamp = date('Y-m-d', strtotime($dateField . ' +168 hours'));

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
    public function testOffsetDateUsingHours(): void {

        $offset = 5;
        $nowStamp = date('Y-m-d H:i', strtotime('now + 5 hours'));

        $offsetHours = 5/24;// hours to minuts to seconds

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
            'Offset date using hours should return the datetime 5 hours in the future'
        );

    }

    /**
     * Test for {@see ADOConnection::offsetDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffsetDateUsingNegativeHours(): void {

        $offset = -5;
        $nowStamp = date('Y-m-d H:i', strtotime('now -5 hours'));

        $offsetHours = -5/24; // Convert days to hours

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
            'Offset date using negative hours should return the date 5 hours in the past'
        );
    }

    /**
     * Test for {@see ADOConnection::offsetDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffsetDateUsingFraction(): void {

        
        $nowStamp = date('Y-m-d H:i', strtotime('now +1440 seconds'));

        $offsetHours = 1440/(24*3600); // Convert days to hours

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
            'Offset date using date fraction should return the date 1440 seconds in the future'
        );
    }

    /**
     * Test for {@see ADOConnection::offsetDate()) using offsetDate to construct a value
     * and writing it back into the database
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffsetWriteUsingFractionAndDateField(): void
    {

        $nowStamp = date('Y-m-d H:i', strtotime('now +1440 seconds'));

        $offset = 1440/(24*3600); // Convert days to hours

        /*
        * Set the base date column to the current time 
        */
        $sql = "UPDATE testtable_3
                   SET date_field={$this->db->offsetDate($offset, false, true)}
                 WHERE number_run_field=8";

        $this->db->execute($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $sql = "SELECT {$GLOBALS['DriverControl']->dateField}
                  FROM testtable_3 
                 WHERE number_run_field=8";
        list($errno, $errmsg) = $this->assertADOdbError('offsetDate()');

        $od = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $nowStamp,
            $od,
            'Offset date using a column as the base date should ' .
            'return the date 1440 seconds in the future based on the date_field column'
        );
    }

     /**
     * Test for {@see ADOConnection::offsetDate()) Simulating the type of update used
     * for session management
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:offsetdate
     *
     * @return void
     */
    public function testOffsetReadUsingFractionAndDateField(): void
    {

        $nowStamp = date('Y-m-d H:i', strtotime('now +1440 seconds'));

        $offset = 1440/(24*3600); // Convert days to hours

        /*
        * Set the base date column to the current time 
        */
        $sql = "UPDATE testtable_3
                   SET date_field={$this->db->sysTimeStamp}
                 WHERE number_run_field=8";

        $this->db->execute($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $sql = "SELECT {$this->db->offsetDate($offset, 'date_field')}
                  FROM testtable_3 
                 WHERE number_run_field=8";
        list($errno, $errmsg) = $this->assertADOdbError('offsetDate()');

        $od = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $nowStamp,
            $od,
            'Offset date using a column as the base date should ' .
            'return the date 1440 seconds in the future based on the date_field column'
        );
    }

}
