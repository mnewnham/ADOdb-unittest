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
     * @dataProvider providerTestGetAssoc
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:getall
     */
    public function testGetAssoc(
        int $fetchMode,
        array $expectedValue,
        string $sql,
        mixed $bind,
        bool $forceArray = false,
        bool $first2Cols = false
    ): void {

        $this->db->setFetchMode($fetchMode);
        $this->db->startTrans();

        $returnedRows = $this->db->getAssoc($sql, $bind, $forceArray, $first2Cols);

        $this->db->completeTrans();

        $this->assertSame(
            $expectedValue,
            $returnedRows,
            'getAssoc() should return expected rows using casing ' . ADODB_ASSOC_CASE
        );
    }

    /**
     * Data provider for {@see testGetAssoc()}
     *
     * @return array [int fetchmode, array expected result, string sql, ?array bind]
     */
    public function providerTestGetAssoc(): array
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
                        2 => 'LINE 2',
                        3 => 'LINE 3',
                        4 => 'LINE 4',
                        5 => 'LINE 5',
                        6 => 'LINE 6',
                    ),
                     "SELECT number_run_field,varchar_field 
                        FROM testtable_3 
                       WHERE number_run_field BETWEEN 2 AND 6
                    ORDER BY number_run_field", false
                ],

            'Bound, FETCH_NUM' =>
                [ADODB_FETCH_NUM,
                     array(
                        2 => 'LINE 2',
                        3 => 'LINE 3',
                        4 => 'LINE 4',
                        5 => 'LINE 5',
                        6 => 'LINE 6',
                     ),
                    "SELECT number_run_field,varchar_field 
                       FROM testtable_3 
                      WHERE number_run_field BETWEEN $p1 AND $p2
                   ORDER BY number_run_field", $bind
                ],

            'Bound, FETCH_BOTH' =>
                [ADODB_FETCH_BOTH,
                     array(
                        2 => 'LINE 2',
                        3 => 'LINE 3',
                        4 => 'LINE 4',
                        5 => 'LINE 5',
                        6 => 'LINE 6',
                     ),
                    "SELECT number_run_field,varchar_field 
                       FROM testtable_3 
                      WHERE number_run_field BETWEEN $p1 AND $p2
                   ORDER BY number_run_field", $bind
                ],

                'Bound, FETCH_BOTH,overflow' =>
                [ADODB_FETCH_BOTH,
                     array(
                        2 => array('VARCHAR_FIELD' => 'LINE 2','ID' => 2),
                        3 => array('VARCHAR_FIELD' => 'LINE 3','ID' => 3),
                        4 => array('VARCHAR_FIELD' => 'LINE 4','ID' => 4),
                        5 => array('VARCHAR_FIELD' => 'LINE 5','ID' => 5),
                        6 => array('VARCHAR_FIELD' => 'LINE 6','ID' => 6)
                     ),
                    "SELECT number_run_field,varchar_field,id
                       FROM testtable_3 
                      WHERE number_run_field BETWEEN $p1 AND $p2
                   ORDER BY number_run_field", $bind
                ],

                'Unbound, FETCH_ASSOC,ASSOC_CASE_UPPER,First 2 Cols' =>
                [ADODB_FETCH_ASSOC,
                    array(
                        2 => 'LINE 2',
                        3 => 'LINE 3',
                        4 => 'LINE 4',
                        5 => 'LINE 5',
                        6 => 'LINE 6',
                    ),
                     "SELECT number_run_field,varchar_field 
                        FROM testtable_3 
                       WHERE number_run_field BETWEEN 2 AND 6
                    ORDER BY number_run_field",
                    false,
                    false,
                    true
                ],

                'Unbound, FETCH_ASSOC,ASSOC_CASE_UPPER,Force array false' =>
                [ADODB_FETCH_ASSOC,
                    array(
                        2 => 'LINE 2',
                        3 => 'LINE 3',
                        4 => 'LINE 4',
                        5 => 'LINE 5',
                        6 => 'LINE 6',
                    ),
                     "SELECT number_run_field,varchar_field,id
                        FROM testtable_3 
                       WHERE number_run_field BETWEEN 2 AND 6
                    ORDER BY number_run_field",
                    false,
                    true,
                    true
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
                    ORDER BY number_run_field", false],

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
        return array();
    }
}
