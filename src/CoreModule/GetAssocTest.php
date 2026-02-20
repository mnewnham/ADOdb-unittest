<?php

/**
 * Tests cases for core SQL functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\CoreModule;

use PhpParser\Node\Stmt\TraitUse;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb Core functions
 */

class GetAssocTest extends ADOdbCoreSetup
{
    /**
     * Test for {@see ADODConnection::getAll()}
     *
     * @param int    $fetchMode     The ADODB_FETCH_MODE to use
     * @param array  $expectedValue The expected value
     * @param string $sql           The SQL Statement to use
     * @param ?array $bind          Optional Bind
     * @param bool   $forceArray    Optional method arg
     * @param bool   $first2Cols    Optional method arg
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:getall
     */
    #[DataProvider('providerTestGetAssoc')]
    public function testGetAssoc(
        array $expectedValue,
        string $fields,
        bool $bindFlag,
        bool $forceArray,
        bool $first2Cols
    ): void {

        $unboundSql = "SELECT $fields
                FROM testtable_3 
                WHERE number_run_field BETWEEN 2 AND 6
                ORDER BY number_run_field";

        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $p2 = $GLOBALS['ADOdbConnection']->param('p2');
        $bind = array('p1' => 2,
                      'p2' => 6
                    );

        $boundSql = "SELECT $fields
                FROM testtable_3 
                WHERE number_run_field BETWEEN $p1 AND $p2
                ORDER BY number_run_field";

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->insertFetchMode($fetchMode);

            $this->db->startTrans();


            if ($bindFlag) {
                $returnedRows = $this->db->getAssoc($boundSql, $bind, $forceArray, $first2Cols);
            } else {
                $returnedRows = $this->db->getAssoc($unboundSql, false, $forceArray, $first2Cols);
            }

            $this->validateResetFetchModes();

            $this->db->completeTrans();

            print "\n------------- FFFF ----\n";
            print_r($expectedValue);
            print_r($returnedRows);

            $this->assertSame(
                $expectedValue,
                $returnedRows,
                sprintf(
                    '[FETCH MODE %s ], Force Array=%s, First 2 Cols=%s should return expected rows',
                    $fetchModeName,
                    $forceArray,
                    $first2Cols
                )
            );
        }
    }

    /**
     * Data provider for {@see testGetAssoc()}
     *
     * @return array [int fetchmode, array expected result, string sql, ?array bind]
     */
    public static function providerTestGetAssoc(): array
    {


        $baseAssociativeArray =  [
            2 => 'LINE 2',
            3 => 'LINE 3',
            4 => 'LINE 4',
            5 => 'LINE 5',
            6 => 'LINE 6',
        ];

        $testArray = [];

        //forceArray / first2cols

        return [
            'Unbound, associative key/value pair' => [
                $baseAssociativeArray,
                'number_run_field,varchar_field',
                false,
                false,
                false
            ],

            'Bound, associative key/value pair' => [
                $baseAssociativeArray,
                'number_run_field,varchar_field',
                true,
                false,
                false
            ],

            'Bound, 2 fields, force array true' => [
                [
                    2 => [ 2 ],
                    3 => [ 3 ],
                    4 => [ 4 ],
                    5 => [ 5 ],
                    6 => [ 6 ]
                ],
                "number_run_field,varchar_field,id",
                true,
                true,
                false
            ],

            'Bound, overflow 3 fields, force array true' => [
                [
                    2 => [ 'LINE 2', 2 ],
                    3 => [ 'LINE 3', 3 ],
                    4 => [ 'LINE 4', 4 ],
                    5 => [ 'LINE 5', 5 ],
                    6 => [ 'LINE 6', 6 ]
                ],
                "number_run_field,varchar_field,id",
                true,
                true,
                false
            ],

            'Unbound, No overfow, First 2 Cols' =>
            [
                $baseAssociativeArray,
                'number_run_field,varchar_field',
                false,
                false,
                true
            ],

            'Unbound, overflow, First 2 of 3 Cols' => [
                $baseAssociativeArray,
                "number_run_field,varchar_field ,id",
                false,
                false,
                true
            ],

            'Unbound, 3 cols, Force array false' => [
                $baseAssociativeArray,
                'number_run_field,varchar_field,id',
                false,
                false,
                false
            ],

        ];

        return array();
    }
}
