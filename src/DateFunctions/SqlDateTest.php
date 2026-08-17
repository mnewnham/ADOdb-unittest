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
class SqlDateTest extends ADOdbTestCase
{
    public static function setUpBeforeClass(): void
    {
        $GLOBALS['ADOdbConnection']->_errorCode = 0;
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
    public function testSqlDate(int $testMethod, string $format, ?int $timestamp, int $margin=0): void
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
                
                if ($format == 'Q') {
                    $expected = ceil((new \DateTime)->format('n') / 3);
                } else {
                    $expected = date($format);
                }
              
                $sql = sprintf(
                    $GLOBALS['DriverControl']->dateMethodExecutor,
                    $this->db->sqlDate($format)
                );

                list($errno, $errmsg) = $this->assertADOdbError('sqlDate()');
                $actual = $this->db->getOne($sql);
                list($errno, $errmsg) = $this->assertADOdbError($sql);

                $message = 'sqlDate should return the portion of the ' .
                'current timestamp identified by the format string [ ' . $format . ' ]';
                break;
            case 3:

                $sql = "SELECT id,{$GLOBALS['DriverControl']->dateTimeField}
                        FROM testtable_3 
                        WHERE datetime_field IS NOT NULL ";

                $this->db->storeFetchModes();
                $this->db->setFetchMode(ADODB_FETCH_NUM);

                $result = $this->db->selectLimit($sql, 1);
                $baseData = $result->fetchRow();

                $this->db->restoreFetchModes();

                list($errno, $errmsg) = $this->assertADOdbError($sql);

                $baseData = array_values($baseData);

              

                 if ($format == 'Q') {
                    $expected = ceil((new \DateTime($baseData[1]))->format('n') / 3);
                } else {
                    $expected = date($format, strtotime($baseData[1]));
                }
                

                $dtSql =  $this->db->sqlDate($format, 'datetime_field');
                $sql = sprintf(
                    "SELECT %s, {$GLOBALS['DriverControl']->dateTimeField} 
                   FROM testtable_3
                    WHERE id=%s",
                    $dtSql,
                    $baseData[0]
                );


                list($errno, $errmsg) = $this->assertADOdbError('sqlDate()');

                $row = array_values($this->db->getRow($sql));
                $actual = $row[0];

                list($errno, $errmsg) = $this->assertADOdbError($sql);

                $message = 'When the SQL [' . $sql . '] is executed, sqlDate should return the portion of the ' .
                'date field identified by the format string [ ' . $format . ' ]. ' . 
                ' The raw value of the field is [' . $row[1] . ']';
                break; 

                default:
                $this->fail("Invalid test method: $testMethod");
        }

        $message .= '. This may be caused by the difference in Time or Timezone of' .
        ' the server if it is on a different machine than the client. ';

        if (preg_match('/^[0-9]+$/', $expected) && $actual && preg_match('/^[0-9]+$/', $actual)) {
            $range = [
                'from' => $expected - $margin,
                'to'   => $expected + $margin
            ];

            $success = ($actual >= $range['from'] && $actual <= $range['to'] ) ? true : false; 
            $message = sprintf(
                '%s The value should be between %s and %s, actually %s',
                $message,
                $range['from'],
                $range['to'],
                $actual
            );
        } else {
            $success = strcmp($expected, $actual ?? '') !== false ? true : false;
            $message = sprintf(
                '$s - Expected %s, got %s',
                $message,
                $expected.
                $actual
            );
        }

        $this->assertTrue(
            $success,
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
            '4 Digit Year' => [2, 'Y', $testNowTimestamp],
            '2 Digit Month' => [2, 'm', $testNowTimestamp],
            '3 Char Month' => [2, 'M', $testNowTimestamp],
            '2 Digit Day Of Month' => [2, 'd', $testNowTimestamp],
            '2 Digit 24 Hour Of Day' => [2, 'H', $testNowTimestamp],
            '2 Digit 12 Hour Of Day' => [2, 'h A', $testNowTimestamp],
            '2 Digit Minute Of Hour' => [2, 'i', $testNowTimestamp],
            'Day Of Week' => [2, 'w', $testNowTimestamp],
            'Character Day Of Week' => [2, 'I', $testNowTimestamp],
            'Week Of Year' => [2, 'W', $testNowTimestamp],
            'Quarter Of Year' => [2, 'Q', $testNowTimestamp],
            '2 Digit Second Of Minute' => [2, 's', $testNowTimestamp, 5],


            'Now 4 Digit Year' => [3, 'Y', null],
            'Now 2 Digit Month' => [3, 'm', null],
            'Now 3 Char Month' => [3, 'M', null],
            'Now 2 Digit Day Of Month' => [3, 'd', null],
            'Now 2 Digit 24 Hour Of Day' => [3, 'H', null],
            'Now 2 Digit 12 Hour Of Day' => [3, 'h A', null],
            'Now 2 Digit Minute Of Hour' => [3, 'i', null],
            'Now Day Of Week' => [3, 'w', null],
            'Now Character Day Of Week' => [3, 'I', null],
            'Now Week Of Year' => [3, 'W', null],
            'Now Quarter Of Year' => [3, 'Q', null],
            'Now 2 Digit Second Of Minute' => [3, 's', null, 5],
        ];
    }

}
