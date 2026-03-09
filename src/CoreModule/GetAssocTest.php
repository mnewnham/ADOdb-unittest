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
     * Test for {@see ADODConnection::getAssoc()}
     *
     * @param array  $expectedNumericValue     The expected value if mode is numeric
     * @param array  $expectedAssociativeValue The expected value if mode is associative
     * @param array  $expectedBothValue        The expected value if mode is both
     * @param string $fields                   The fields to return
     * @param ?array $bindFlag                 Optional Bind
     * @param bool   $forceArray               Optional method arg
     * @param bool   $first2Cols               Optional method arg
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:getassoc
     */
    #[DataProvider('providerTestGetAssoc')]
    public function testGetAssoc(
        array $expectedNumericValue,
        array $expectedAssociativeValue,
        array $expectedBothValue,
        string $fields,
        bool $bindFlag,
        bool $forceArray,
        int $first2Cols
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
            
            $absoluteFetchMode = $this->insertFetchMode($fetchMode);

            $this->db->startTrans();


            if ($bindFlag) {
                $returnedRows = $this->db->getAssoc($boundSql, $bind, $forceArray, $first2Cols);
            } else {
                $returnedRows = $this->db->getAssoc($unboundSql, false, $forceArray, $first2Cols);
            }


            $this->validateResetFetchModes();

            $this->db->completeTrans();

            switch ($absoluteFetchMode) {
                case ADODB_FETCH_NUM:
                    $expectedValue = $expectedNumericValue;
                    break;
                case ADODB_FETCH_ASSOC:
                    $expectedValue = $expectedAssociativeValue;
                    break;
                case ADODB_FETCH_BOTH:
                    $expectedValue = $expectedBothValue;
                    break;

            }

            foreach ($returnedRows as $key => $value) {
                if (!is_array($value)) {
                    $returnedRows[$key] = (string)$value;
                    continue;
                }
                foreach ($value as $vKey => $vValue) {
                    $returnedRows[$key][$vKey] = (string)$vValue;
                }
            }

            $this->assertSame(
                $expectedValue,
                $returnedRows,
                sprintf(
                    '[FETCH MODE %s ][ABS %s], Force Array=%s, First 2 Cols=%s should return expected rows',
                    $fetchModeName,
                    $this->testFetchModes[$fetchMode],
                    $forceArray,
                    $first2Cols
                )
            );
        }
    }


    /**
     * Test for {@see ADODConnection::getAssoc()}
     *
     * @param array  $expectedNumericValue     The expected value if mode is numeric
     * @param array  $expectedAssociativeValue The expected value if mode is associative
     * @param array  $expectedBothValue        The expected value if mode is both
     * @param string $fields                   The fields to return
     * @param ?array $bindFlag                 Optional Bind
     * @param bool   $forceArray               Optional method arg
     * @param bool   $first2Cols               Optional method arg
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:getassoc
     */
    #[DataProvider('providerTestGetAssoc')]
    public function testGetAssocViaRecordSet(
        array $expectedNumericValue,
        array $expectedAssociativeValue,
        array $expectedBothValue,
        string $fields,
        bool $bindFlag,
        bool $forceArray,
        int $first2Cols
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
            
            $absoluteFetchMode = $this->insertFetchMode($fetchMode);

            $this->db->startTrans();


            if ($bindFlag) {
                $returnedRows = $this->db->execute($boundSql, $bind)->getAssoc($forceArray, $first2Cols);
            } else {
                $returnedRows = $this->db->execute($unboundSql, false)->getAssoc($forceArray, $first2Cols);
            }


            $this->validateResetFetchModes();

            $this->db->completeTrans();

            switch ($absoluteFetchMode) {
                case ADODB_FETCH_NUM:
                    $expectedValue = $expectedNumericValue;
                    break;
                case ADODB_FETCH_ASSOC:
                    $expectedValue = $expectedAssociativeValue;
                    break;
                case ADODB_FETCH_BOTH:
                    $expectedValue = $expectedBothValue;
                    break;

            }

            foreach ($returnedRows as $key => $value) {
                if (!is_array($value)) {
                    $returnedRows[$key] = (string)$value;
                    continue;
                }
                foreach ($value as $vKey => $vValue) {
                    $returnedRows[$key][$vKey] = (string)$vValue;
                }
            }

            $this->assertSame(
                $expectedValue,
                $returnedRows,
                sprintf(
                    '[RECORDSET MODE][FETCH MODE %s ][ABS %s], Force Array=%s, First 2 Cols=%s should return expected rows',
                    $fetchModeName,
                    $this->testFetchModes[$fetchMode],
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


        $baseArray =  [
            2 => 'LINE 2',
            3 => 'LINE 3',
            4 => 'LINE 4',
            5 => 'LINE 5',
            6 => 'LINE 6',
        ];

        
        $baseNumeric2Array = [
            2 => [ '2', 'LINE 2'],
            3 => [ '3', 'LINE 3'],
            4 => [ '4', 'LINE 4' ],
            5 => [ '5', 'LINE 5' ],
            6 => [ '6', 'LINE 6' ]
        ];

        $baseAssociative2Array =  [
            2 => ['number_run_field' => '2', 'varchar_field'=> 'LINE 2'],
            3 => ['number_run_field' => '3', 'varchar_field'=> 'LINE 3'],
            4 => ['number_run_field' => '4', 'varchar_field'=> 'LINE 4'],
            5 => ['number_run_field' => '5', 'varchar_field'=> 'LINE 5'],
            6 => ['number_run_field' => '6', 'varchar_field'=> 'LINE 6'],
        ];

        $baseBoth2Array = [
            2 => [
                0 => '2',
                'number_run_field' => '2',
                1 => 'LINE 2',
                'varchar_field'=> 'LINE 2'          
                ],
            3 => [
                 0 => '3',
                'number_run_field' => '3',
                1 => 'LINE 3',
                'varchar_field'=> 'LINE 3'          
                ],
            4 => [
                 0 => '4',
                'number_run_field' => '4',
                1 => 'LINE 4',
                'varchar_field'=> 'LINE 4'          
                ],
            5 => [
                 0 => '5',
                'number_run_field' => '5',
                1 => 'LINE 5',
                'varchar_field'=> 'LINE 5'          
                ],
            6 => [
                0 => '6',
                'number_run_field' => '6',
                1 => 'LINE 6',
                'varchar_field'=> 'LINE 6'          
                ],
        ];

        $baseNumeric3Array = [
            2 => [ '2', 'LINE 2', '2' ],
            3 => [ '3', 'LINE 3', '3' ],
            4 => [ '4', 'LINE 4', '4' ],
            5 => [ '5', 'LINE 5', '5' ],
            6 => [ '6', 'LINE 6', '6' ]
        ];

        $baseAssociative3Array =  [
            2 => ['number_run_field' => '2', 'varchar_field'=> 'LINE 2', 'id' => '2'],
            3 => ['number_run_field' => '3', 'varchar_field'=> 'LINE 3', 'id' => '3'],
            4 => ['number_run_field' => '4', 'varchar_field'=> 'LINE 4', 'id' => '4'],
            5 => ['number_run_field' => '5', 'varchar_field'=> 'LINE 5', 'id' => '5'],
            6 => ['number_run_field' => '6', 'varchar_field'=> 'LINE 6', 'id' => '6'],
        ];

        $baseBoth3Array = [
            2 => [
                0 => '2',
                'number_run_field' => '2', 
                1 => 'LINE 2',
                'varchar_field'=> 'LINE 2',
                2 => '2',
                'id' => '2'
                ],
            3 => [
                0 => '3',
                'number_run_field' => '3',
                1 => 'LINE 3',
                'varchar_field'=> 'LINE 3',
                2 => '3',
                'id' => '3'
                ],
            4 => [
                0 => '4',
                'number_run_field' => '4',
                1 => 'LINE 4',
                'varchar_field'=> 'LINE 4',
                2 => '4',
                'id' => '4',
                ],
            5 => [
                0 => '5',
                'number_run_field' => '5',
                1 => 'LINE 5',
                'varchar_field'=> 'LINE 5',
                2 => '5',
                'id' => '5'
                ],
            6 => [
                0 => '6',
                'number_run_field' => '6',
                1 => 'LINE 6',
                'varchar_field'=> 'LINE 6',
                2 => '6',
                'id' => '6'
                ],
        ];

        if ($GLOBALS['DriverControl']->fetchBothManner == 1) {
            
            $bb2A           = $baseBoth2Array;
            $baseBoth2Array = [];
            foreach ($bb2A as $key => $data) {
                $keys   = array_keys($data);
                $values = array_values($data);
                               
                $newData     = [];
                $outputIndex = [1, 0, 3, 2];
                
                foreach  ($outputIndex as $entry) {
                    $newData[$keys[$entry]] = $values[$entry];
                }

                $baseBoth2Array[$key] = $newData;
            }

            $bb3A           = $baseBoth3Array;
            $baseBoth3Array = [];
            foreach ($bb3A as $key => $data) {
                $keys   = array_keys($data);
                $values = array_values($data);
                
                $newData     = [];
                $outputIndex = [1, 0, 3, 2, 5, 4];
                
                foreach  ($outputIndex as $entry) {
                    $newData[$keys[$entry]] = $values[$entry];
                }

                $baseBoth3Array[$key] = $newData;
            }
        }

        if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
             foreach($baseAssociative2Array as $key => $value) {
                $uValue = array_change_key_case($value, CASE_UPPER);
                $baseAssociative2Array[$key] = $uValue;
            }

            foreach($baseAssociative3Array as $key => $value) {
                $uValue = array_change_key_case($value, CASE_UPPER);
                $baseAssociative3Array[$key] = $uValue;
            }

            foreach($baseBoth2Array as $key => $value) {
                $uValue = array_change_key_case($value, CASE_UPPER);
                $baseBoth2Array[$key] = $uValue;
            }

            foreach($baseBoth3Array as $key => $value) {
                $uValue = array_change_key_case($value, CASE_UPPER);
                $baseBoth3Array[$key] = $uValue;
            }
        }
       
        return [
            'T1, Unbound, associative key/value pair' => [
                $baseArray,
                $baseArray,
                $baseArray,
                'number_run_field,varchar_field',
                false,
                false,
                0
            ],

            'T2, Bound, associative key/value pair' => [
                $baseArray,
                $baseArray,
                $baseArray,
                'number_run_field,varchar_field',
                true,
                false,
                0
            ],

            'T3, Bound, 3 fields [number_run_field,varchar_field,id] , force array true, first2=true' => [
                $baseNumeric2Array,
                $baseAssociative2Array,
                $baseBoth2Array,
                "number_run_field,varchar_field,id",
                true,
                true,
                1
            ],

            'T4, Bound, overflow 3 fields, force array true first2=false' => [
                $baseNumeric3Array,
                $baseAssociative3Array,
                $baseBoth3Array,
                "number_run_field,varchar_field,id",
                true,
                true,
                0
            ],

            'T5, Unbound, No overfow, First 2 Cols' =>
            [
                $baseArray,
                $baseArray,
                $baseArray,
                'number_run_field,varchar_field',
                false,
                false,
                1
            ],

            'T6, Unbound, overflow, First 2 of 3 Cols, $forceArray=0, $first2Cols=1' => [
                $baseArray,
                $baseArray,
                $baseArray,
                "number_run_field,varchar_field ,id",
                false,
                false,
                1
            ],

            'T7, Unbound, 3 cols, Force array false, $force_array=false,$first2Cols=false' => [
               
                $baseNumeric3Array,
                $baseAssociative3Array,
                $baseBoth3Array,
                'number_run_field,varchar_field,id',
                false,
                false,
                0
            ],

        ];

        return array();
    }
}
