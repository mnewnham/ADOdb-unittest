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
 * @copyright 2025 Mark Newnham, Damien Regad and the ADOdb community
 * @license   MIT https://google.com
 *
 * @link https://github.com/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 */
use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
#[RequiresPhpExtension('sqlsrv')]
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
     * @param string $result     The expected result
     *
     * @dataProvider providerSqlDate
     *
     * @return void
     */
    public function testSqlDate(
        string $dateFormat,
        string $field,
        string $region,
        string $result
    ): void {

        if ($this->skipFollowingTests) {
            return;
        }

        $formatDate = "{$this->db->sqlDate($dateFormat,$field)}";

        $sql = "SELECT testdate, $formatDate $region, null 
                  FROM (
                SELECT CONVERT(
                        DATETIME,'2016-12-17 18:55:30.590' ,121
                        ) testdate,
                       CONVERT(
                       DATETIME,'2016-01-01 18:55:30.590' ,121
                       ) testdatesmall,
                null nulldate
                ) q ";

        $res = $this->db->GetRow($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            $res['region'],
            $result,
            'SQL Date format for region ' . $region . ' should match expected format'
        );
    }

    /**
     * Data provider for testSqlDate
     *
     * @return array
     */
    public function providerSQLDate(): array
    {
        return [

            ["d/m/Y", "testdate" ," FR4","17/12/2016"],
            ["d/m/y", "testdate" ," FR4b", "17/12/2016",],
            ["d/m/Y", "NULL", "nullFR4", null],
            ["m/d/Y", "testdate" , " US4", "12/17/2016"],
            ["m/d/y", "testdate" , " US4b", "12/17/2016"],
            ["m-d-Y", "testdate" , " USD4", "17-12-2016"],
            ["m-d-y", "testdate" , " USD4b", "17-12-2016"],
            ["Y.m.d", "testdate" , " ANSI4", "2016.12.17"],
            ["d.m.Y", "testdate" , " GE4", "17.12.2016"],
            ["d.m.y", "testdate" , " GE4b", "17.12.2016"],
            ["d-m-Y", "testdate" , " IT4", "17-12-2016"],
            ["d-m-y", "testdate" , " IT4b", "17-12-2016"],
            ["Y/m/d", "testdate" , " Japan4", "2016/12/17"],
            ["y/m/d", "testdate" , " Japan4b", "2016/12/17"],
            ["H:i:s", "testdate" ,  " timeonly","18:55:30"],
            ["d m Y",  "testdate" ," Space4","17 12 2016"],  // Is done by former method
            ["d m Y",  "NULL" ," nullSpace4","null"],
            ["m-d-Y","testdatesmall"," nowUSdash4","01-01-2016"]
        ];
    }
}
