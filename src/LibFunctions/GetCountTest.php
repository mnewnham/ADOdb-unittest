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
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb Core functions
 */
class GetCountTest extends ADOdbCoreSetup
{
    /**
     * Test for _adodb_getcount
     *
     *
     * @param int    $expectedResult
     * @param string $sql
     * @param ?array $bind
     *
     * @return void
     */
    #[DataProvider('providerTestGetCount')]
    public function testGetCount(
        string $expectedResult,
        string $sql,
        mixed $bind
    ): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->insertFetchMode($fetchMode);

            $response = _adodb_getcount(
                $this->db, 
                $sql,
                $bind,
                $secs2cache=0
            );

            $this->assertSame(
                $expectedResult,
                (string)$response,
                sprintf(
                    '[%s] ADOConnection::GetCount',
                    $fetchDescription
                )
            );
            
        }
    }

    /**
     * Data provider for getCount
     *
     * @return array [string sql, ?array bind, int $expectedResult]
     */
    public static function providerTestGetCount(): array
    {

        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 10);

        return [
            'Base SQL' => [
                '11',
                "SELECT * FROM testtable_3",
                false
            ],
            'Count number run, unbound' => [
                '2',
                "SELECT * FROM testtable_3 WHERE number_run_field>=10",
                false
            ],
            'Count number run, bound' => [
                '2',
                "SELECT * FROM testtable_3 WHERE number_run_field>=$p1",
                $bind
            ],
            'Count number run join testtable_1, unbound' => [
                '0',
                "SELECT * FROM testtable_3 tt3,testtable_1 tt1
                  WHERE tt3.number_run_field>=10
                    AND tt1.number_run_field=tt3.number_run_field",
                false
            ],
             'Count number run join testtable_1, bound' => [
                '0',
                "SELECT * FROM testtable_3 tt3,testtable_1 tt1
                  WHERE tt3.number_run_field>=$p1
                    AND tt1.number_run_field=tt3.number_run_field",
                $bind
            ],
            'Count number run left join testtable_1, unbound' => [
                '2',
                "SELECT * FROM testtable_3 tt3
                LEFT JOIN testtable_1 tt1
                  ON tt1.number_run_field=tt3.number_run_field
                  WHERE tt3.number_run_field>=10",
                false
            ],
            'Count number run right join testtable_1, unbound' => [
                '0',
                "SELECT tt3.* 
                   FROM testtable_3 tt3
                RIGHT JOIN testtable_1 tt1
                  ON tt1.number_run_field=tt3.number_run_field
                  WHERE tt3.number_run_field>=10",
                false
            ],
            'Count number run subselect testtable_1, unbound' => [
                '2',
                "SELECT tt3.*, (SELECT MAX tt1.number_run_field FROM testtable_1 tt1) tt1_max
                   FROM testtable_3 tt3
                   WHERE tt3.number_run_field>=10",
                false
            ],
        ];
    }
}