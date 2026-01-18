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
 * @copyright 2025 Mark Newnham, Damien Regad and the ADOdb community
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 *
 * @link https://github.com/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */
namespace MNewnham\ADOdbUnitTest\CoreModule;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb Core functions
 */
class GetAllTest extends ADOdbCoreSetup
{
    /**
     * Test for {@see ADODConnection::getAll()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:getall
     *
     * @param int $fetchMode
     * @param array $expectedValue
     * @param string $sql
     * @param ?array $bind
     *
     * @return void
     *
     * @dataProvider providerTestGetAll
     */
    public function testGetAll(int $fetchMode, array $expectedValue, string $sql, ?array $bind): void
    {
        $this->db->setFetchMode($fetchMode);
        $this->db->startTrans();

        if ($bind) {
            $returnedRows = $this->db->getAll($sql, $bind);
        } else {
            $returnedRows = $this->db->getAll($sql);
        }

        list($errno,$errmsg) = $this->assertADOdbError($sql, $bind);

        $this->db->completeTrans();

        if ($fetchMode == ADODB_FETCH_BOTH) {
            $expectedValue = $this->sortFetchBothRecords($expectedValue);
            $returnedRows  = $this->sortFetchBothRecords($returnedRows);
        }
       
        $this->assertSame(
            $expectedValue,
            $returnedRows,
            'getall() should return expected rows using casing ' . 
            $this->testFetchModes[$fetchMode]
        );
    }

    /**
     * Data provider for {@see testGetAll()}
     *
     * @return array [int fetchmode, array expected result, string sql, ?array bind]
     */
    public function providerTestGetAll(): array
    {
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $p2 = $GLOBALS['ADOdbConnection']->param('p2');
        $bind = array('p1' => 2,
                      'p2' => 6
                    );

        switch (ADODB_ASSOC_CASE) {
            case ADODB_ASSOC_CASE_UPPER:
                return [
            'Unbound, FETCH_ASSOC,ASSOC_CASE_UPPER' =>
                [ADODB_FETCH_ASSOC,
                    array(
                        array('VARCHAR_FIELD' => 'LINE 2'),
                        array('VARCHAR_FIELD' => 'LINE 3'),
                        array('VARCHAR_FIELD' => 'LINE 4'),
                        array('VARCHAR_FIELD' => 'LINE 5'),
                        array('VARCHAR_FIELD' => 'LINE 6')
                    ),
                     "SELECT testtable_3.varchar_field 
                        FROM testtable_3 
                       WHERE number_run_field BETWEEN 2 AND 6
                    ORDER BY number_run_field", null
                ],

            'Bound, FETCH_NUM' =>
                [ADODB_FETCH_NUM,
                    array(
                        array('0' => 'LINE 2'),
                        array('0' => 'LINE 3'),
                        array('0' => 'LINE 4'),
                        array('0' => 'LINE 5'),
                        array('0' => 'LINE 6')
                        ),
                    "SELECT testtable_3.varchar_field 
                       FROM testtable_3 
                      WHERE number_run_field BETWEEN $p1 AND $p2
                   ORDER BY number_run_field", $bind
                ],

            'Bound, FETCH_BOTH' =>
                [ADODB_FETCH_BOTH,
                    array(
                        array(
                            '0' => 'LINE 2',
                            'VARCHAR_FIELD' => 'LINE 2'
                        ),
                        array(
                            '0' => 'LINE 3',
                            'VARCHAR_FIELD' => 'LINE 3'
                        ),
                        array(
                            '0' => 'LINE 4',
                            'VARCHAR_FIELD' => 'LINE 4'
                        ),
                        array(
                            '0' => 'LINE 5',
                            'VARCHAR_FIELD' => 'LINE 5'
                        ),
                        array(
                            '0' => 'LINE 6',
                            'VARCHAR_FIELD' => 'LINE 6'
                        )
                    ),
                    "SELECT testtable_3.varchar_field 
                       FROM testtable_3 
                      WHERE number_run_field BETWEEN $p1 AND $p2
                   ORDER BY number_run_field", $bind
                ],


            ];

            break;
            case ADODB_ASSOC_CASE_LOWER:
                return [
            'Unbound, FETCH_ASSOC, ASSOC_CASE_LOWER' =>
                [ADODB_FETCH_ASSOC,
                    array(
                        array('varchar_field' => 'LINE 2'),
                        array('varchar_field' => 'LINE 3'),
                        array('varchar_field' => 'LINE 4'),
                        array('varchar_field' => 'LINE 5'),
                        array('varchar_field' => 'LINE 6')
                    ),
                     "SELECT testtable_3.varchar_field 
                        FROM testtable_3 
                       WHERE number_run_field BETWEEN 2 AND 6
                    ORDER BY number_run_field", null],

            'Bound, FETCH_NUM' =>
                [ADODB_FETCH_NUM,
                    array(
                        array('0' => 'LINE 3'),
                        array('0' => 'LINE 4'),
                        array('0' => 'LINE 5'),
                        array('0' => 'LINE 6')
                        ),
                    "SELECT testtable_3.varchar_field 
                       FROM testtable_3 
                      WHERE number_run_field BETWEEN $p1 AND $p2
                   ORDER BY number_run_field", $bind],
            'Bound, FETCH_BOTH' =>
                [ADODB_FETCH_BOTH,
                    array(
                        array(
                            '0' => 'LINE 2',
                            'varchar_field' => 'LINE 2'
                        ),
                        array(
                            '0' => 'LINE 3',
                            'varchar_field' => 'LINE 3'
                        ),
                        array(
                            '0' => 'LINE 4',
                            'varchar_field' => 'LINE 4'
                        ),
                        array(
                            '0' => 'LINE 5',
                            'varchar_field' => 'LINE 5'
                        ),
                        array(
                            '0' => 'LINE 6',
                            'varchar_field' => 'LINE 6'
                        )
                    ),
                    "SELECT testtable_3.varchar_field 
                       FROM testtable_3 
                      WHERE number_run_field BETWEEN $p1 AND $p2
                   ORDER BY number_run_field", $bind
                ],
                ];

                break;
        }
    }
}
