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

use PHPUnit\Framework\TestCase;
use ADOdbUnitTest\CoreModule;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb Core functions
 */
class SelectLimitTest extends ADOdbCoreSetup
{
    /**
     * Test for {@see ADODConnection::selectlimit]
     *
     * @param int    $fetchMode     The ADOdb fetch mode
     * @param array  $expectedValue The expected result
     * @param string $sql           The SQL statement
     * @param int    $count         The number of records to return
     * @param int    $offset        The start point
     * @param ?array $bind          Any bind array values
     *
     * @return void
     *
     * @dataProvider providerTestSelectLimit
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:selectlimit
     */
    public function testSelectLimit(int $fetchMode, array $expectedValue, string $sql, $count, $offset, ?array $bind): void
    {
        $this->db->setFetchMode($fetchMode);

        $this->db->startTrans();

        if ($bind) {
            $result = $this->db->selectLimit($sql, $count, $offset, $bind);
        } else {
            $result = $this->db->selectLimit($sql, $count, $offset);
        }

        list($errno,$errmsg) = $this->assertADOdbError($sql, $bind);

        $this->db->completeTrans();
        $returnedRows = array();

        foreach ($result as $index => $row) {
            $returnedRows[] = $row;
        }

        $this->assertSame(
            $expectedValue,
            $returnedRows,
            'ADOConnection::selectLimit()'
        );
    }

    /**
     * Data provider for {@see testSelectLimit()}
     *
     * @return array [
     *      int $fetchMode,
     *      array $result,
     *      string $sql,
     *      int rows,
     *      int offset,
     *      ?array $bind
     *      ]
     */
    public function providerTestSelectLimit(): array
    {
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');

        $bind = array(
            'p1' => 3
        );

        if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            return [
                'Select Unbound, FETCH_ASSOC, ASSOC_CASE_UPPER' =>
                    [ADODB_FETCH_ASSOC,
                        array(
                            array('VARCHAR_FIELD' => 'LINE 6'),
                            array('VARCHAR_FIELD' => 'LINE 7'),
                            array('VARCHAR_FIELD' => 'LINE 8'),
                            array('VARCHAR_FIELD' => 'LINE 9')
                        ),
                        "SELECT testtable_3.varchar_field 
                            FROM testtable_3 
                            WHERE number_run_field>3
                        ORDER BY number_run_field", 4, 2, null
                    ],
                'Select, Bound, FETCH_NUM' =>
                    [ADODB_FETCH_NUM,
                        array(
                            array('0' => 'LINE 5'),
                            array('0' => 'LINE 6'),
                            array('0' => 'LINE 7'),
                            array('0' => 'LINE 8')
                            ),
                        "SELECT testtable_3.varchar_field 
                        FROM testtable_3 
                        WHERE number_run_field>=$p1 
                    ORDER BY number_run_field", 4, 2, $bind
                    ],
                'Select, Bound, FETCH_BOTH' =>
                    [ADODB_FETCH_BOTH,
                        array(
                            array(
                                '0' => 'LINE 5',
                                'VARCHAR_FIELD' => 'LINE 5'
                            ),
                            array(
                                '0' => 'LINE 6',
                                'VARCHAR_FIELD' => 'LINE 6'
                            ),
                            array(
                                '0' => 'LINE 7',
                                'VARCHAR_FIELD' => 'LINE 7'
                            ),
                            array(
                                '0' => 'LINE 8',
                                'VARCHAR_FIELD' => 'LINE 8'
                            )
                        ),
                        "SELECT testtable_3.varchar_field 
                        FROM testtable_3 
                        WHERE number_run_field>=$p1 
                    ORDER BY number_run_field", 4, 2, $bind
                    ],
                'Select Unbound, FETCH_ASSOC Get first record, ASSOC_CASE_UPPER' => [
                    ADODB_FETCH_ASSOC,
                    array(
                        array('DATE_FIELD' => '2025-01-01'),
                    ),
                    "SELECT testtable_3.date_field 
                        FROM testtable_3 
                    ORDER BY number_run_field", 1, -1, null
                ],
                 'Select Unbound, FETCH_ASSOC Invalid record, ASSOC_CASE_UPPER' => [
                    ADODB_FETCH_ASSOC,
                        array(),
                        "SELECT testtable_3.date_field 
                            FROM testtable_3 
                        ORDER BY number_run_field", -9, 901, null
                ],
            ];
        } else {
             return [
                'Select Unbound, FETCH_ASSOC, ASSOC_CASE_LOWER' =>
                    [ADODB_FETCH_ASSOC,
                        array(
                            array('varchar_field' => 'LINE 6'),
                            array('varchar_field' => 'LINE 7'),
                            array('varchar_field' => 'LINE 8'),
                            array('varchar_field' => 'LINE 9')
                        ),
                        "SELECT testtable_3.varchar_field 
                            FROM testtable_3 
                            WHERE number_run_field>3
                        ORDER BY number_run_field", 4, 2, null
                    ],
                'Select, Bound, FETCH_NUM' =>
                    [ADODB_FETCH_NUM,
                        array(
                            array('0' => 'LINE 5'),
                            array('0' => 'LINE 6'),
                            array('0' => 'LINE 7'),
                            array('0' => 'LINE 8')
                            ),
                        "SELECT testtable_3.varchar_field 
                        FROM testtable_3 
                        WHERE number_run_field>=$p1 
                    ORDER BY number_run_field", 4, 2, $bind
                    ],
                'Select, Bound, FETCH_BOTH, CASE LOWER' =>
                        [ADODB_FETCH_BOTH,
                            array(
                                array(
                                    '0' => 'LINE 5',
                                    'varchar_field' => 'LINE 5'
                                ),
                                array(
                                    '0' => 'LINE 6',
                                    'varchar_field' => 'LINE 6'
                                ),
                                array(
                                    '0' => 'LINE 7',
                                    'varchar_field' => 'LINE 7'
                                ),
                                array(
                                    '0' => 'LINE 8',
                                    'varchar_field' => 'LINE 8'
                                )
                            ),
                            "SELECT testtable_3.varchar_field 
                            FROM testtable_3 
                            WHERE number_run_field>=$p1 
                        ORDER BY number_run_field", 4, 2, $bind
                ],
                'Select Unbound, FETCH_ASSOC Get first record, ASSOC_CASE_LOWER' => [
                    ADODB_FETCH_ASSOC,
                        array(
                            array('date_field' => '2025-01-01'),
                        ),
                        "SELECT testtable_3.date_field 
                            FROM testtable_3 
                        ORDER BY number_run_field", 1, -1, null
                ],
                'Select Unbound, FETCH_ASSOC Invalid record, ASSOC_CASE_LOWER' => [
                    ADODB_FETCH_ASSOC,
                        array(),
                        "SELECT testtable_3.date_field 
                            FROM testtable_3 
                        ORDER BY number_run_field", 1, -9, null
                ],
             ];
        }
    }
}
