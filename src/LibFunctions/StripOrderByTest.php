<?php

/**
 * Tests cases for Library functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\LibFunctions;

use MNewnham\ADOdbUnitTest\CoreModule\ADOdbCoreSetup;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class stripOrderByTest
 *
 * Test cases for for ADOdb StripOrderBy Library function
 */
class StripOrderByTest extends ADOdbCoreSetup
{
    /**
     * Test for _adodb_strip_order_by
     *
     *
     * @param string $expectedResult
     * @param string $sql
     *
     * @return void
     */
    #[DataProvider('providerTestStripOrderBy')]
    public function testStripOrderBy(
        string $expectedResult,
        string $sql
    ): void {

        $response = adodb_strip_order_by($sql);

        $from = ["\n", "\r"];
        $to   = [" ", " "];
        $this->assertSame(
            $expectedResult,
            trim(str_replace($from, $to, $response)),
            'ADOConnection::adodb_strip_order_by'
        );
    }

    /**
     * Data provider for StripOrderBy
     *
     * @return array [string expectedResult, string $sqlStatement]
     */
    public static function providerTestStripOrderBy(): array
    {

        return [
            'Base SQL' => [
                "SELECT * FROM testtable_3",
                "SELECT * FROM testtable_3 order by number_run_field"
            ],
            'Base SQL Uppercase' => [
                "SELECT * FROM testtable_3",
                "SELECT * FROM testtable_3 ORDER BY number_run_field"
            ],
            'Base SQL Uppercase, NewLine' => [
                "SELECT * FROM testtable_3",
                "SELECT * FROM testtable_3 
                 ORDER BY number_run_field"
            ],
            'Base SQL With String' => [
                "SELECT * FROM testtable_3, 'ORDER BY' orderby",
                "SELECT * FROM testtable_3, 'ORDER BY' orderby order by number_run_field"
            ],
            'Union SQL No Alias' => [
            "SELECT a,b FROM (SELECT a,b FROM testtable_3 UNION SELECT a,b FROM testtable_1)",
            "SELECT a,b FROM (SELECT a,b FROM testtable_3 UNION SELECT a,b FROM testtable_1) ORDER BY b DESC"
            ],
            'Union SQL Alias' => [
            "SELECT a,b, 'ORDER BY' orderby FROM (SELECT a,b FROM testtable_3 UNION SELECT a,b FROM testtable_1) MYALIAS",
            "SELECT a,b, 'ORDER BY' orderby FROM (SELECT a,b FROM testtable_3 UNION SELECT a,b FROM testtable_1) MYALIAS ORDER BY b DESC"
            ]
        ];
    }
}
