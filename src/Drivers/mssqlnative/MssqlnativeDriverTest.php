<?php

/**
 * Tests cases for the mssqlnative driver of ADOdb.
 * Try to write database-agnostic tests where possible.
 *
 * This file is part of ADOdb-unittest, a PHPUnit test suite for
 * the ADOdb Database Abstraction Layer library for PHP.
 *
 * @category  Library
 * @package   ADOdb-unittest
 * @author    Mark Newnham <email@email.com>
 * @copyright 2025,2026 Mark Newnham
 * @license   MIT https://google.com
 *
 * @link https://github.com/mnewnham/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 */

namespace MNewnham\ADOdbUnitTest\Drivers;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MssqlnativeDriverTest
 *
 * Test cases for the ADOdb mssqlnative Driver
 */
class MssqlnativeDriverTest extends ADOdbTestCase
{
    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();

        if ($this->adoDriver !== 'mssqlnative') {
            $this->skipFollowingTests = true;

            $this->markTestSkipped(
                'This test is only applicable for the mssqlnative driver'
            );
            return;
        }
    }

    /**
     * Tear down the test environment
     *
     * @return void
     */
    public function tearDown(): void
    {
    }

    /**
     * Test the SQLDate function. Cloned from the original test_mssqlnative.php
     *
     * @param string $dateFormat The date to test
     * @param string $field      The field to test
     * @param string $region     The region to test
     * @param string $expected   The expected result
     *
     * @return void
     */
    #[DataProvider('providerSQLDate')]
    public function testSqlDate(
        string $dateFormat,
        string $field,
        string $region,
        ?string $expected
    ): void {

        if ($this->skipFollowingTests) {
            return;
        }

        $this->db->setFetchMode(ADODB_FETCH_ASSOC);

        $formatDate = "{$this->db->sqlDate($dateFormat,$field)}";

        $sql = "SELECT test_date, $formatDate $region, null 
                  FROM (
                SELECT CONVERT(
                        DATETIME,'2016-12-17 18:55:30.590' ,121
                        ) test_date,
                       CONVERT(
                       DATETIME,'2016-01-01 18:55:30.590' ,121
                       ) test_datesmall,
                null nulldate
                ) q ";

        $result = $this->db->selectLimit($sql,1);
        $res = $result->fetchRow();
       
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            $expected,
            $res[strtolower($region)],
            'SQL Date format for region ' . $res[strtolower($region)] . ' should match expected format'
        );
    }

    /**
     * Data provider for testSqlDate
     *
     * @return array
     */
    public static function providerSQLDate(): array
    {
        return [

            ["d/m/Y", "test_date" ,"FR4","17/12/2016"],
            ["d/m/y", "test_date" ,"FR4b", "17/12/2016",],
            ["d/m/Y", "NULL", "nullFR4", NULL ],
            ["m/d/Y", "test_date" , "US4", "12/17/2016"],
            ["m/d/y", "test_date" , "US4b", "12/17/2016"],
            ["m-d-Y", "test_date" , "USD4", "12-17-2016"],
            ["m-d-y", "test_date" , "USD4b", "12-17-2016"],
            ["Y.m.d", "test_date" , "ANSI4", "2016.12.17"],
            ["d.m.Y", "test_date" , "GE4", "17.12.2016"],
            ["d.m.y", "test_date" , "GE4b", "17.12.2016"],
            ["d-m-Y", "test_date" , "IT4", "17-12-2016"],
            ["d-m-y", "test_date" , "IT4b", "17-12-2016"],
            ["Y/m/d", "test_date" , "Japan4", "2016/12/17"],
            ["y/m/d", "test_date" , "Japan4b", "2016/12/17"],
            ["H:i:s", "test_date" ,  "timeonly","18:55:30"],
            ["d m Y",  "test_date" ,"Space4","17 12 2016"],  // Is done by former method
            ["d m Y",  "NULL" ,"nullSpace4", NULL],
            ["m-d-Y","test_date","nowUSdash4","01-01-2016"]
        ];
    }
}

