<?php

/**
 * Tests cases for the mysqli driver of ADOdb.
 * Try to write database-agnostic tests where possible.
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

namespace MNewnham\ADOdbUnitTest\Drivers;

use MNewnham\ADOdbUnitTest\Drivers\ADOdbCustomDriver;
use PHPUnit\Framework\Attributes\DataProvider;
/**
 * Class MysqliDriverTest
 *
 * Test cases for for the MySQLi native driver
 */
class MysqliDriverTest extends ADOdbCustomDriver
{
     /**
     * The DB Physical identifier must be set in the
     * overload class
     *
     * @example MYSQLI_TYPE_JSON
     * @var     mixed $physicalType
     */
    protected mixed $physicalType = MYSQLI_TYPE_JSON;

    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();

        if ($this->adoDriver !== 'mysqli') {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'This test is only applicable for the mysqli driver'
            );
        }

        $this->physicalType = MYSQLI_TYPE_JSON;
        $this->columnType   = 'JSON';
    }

    //#[DataProvider('providerMultiQueryWithStoredProcedure')]
    public function testMultiQueryWithStoredProcedure(): void
    {
        //$this->db->multiQuery = true;

        $row1_1 = ['a','b','c'];
        $row1_2 = ['row1_1' => 'a', 'row1_2' => 'b', 'row1_3' => 'c'];
        $row1_3 = [
            0 => 'a',
            'row1_1' => 'a',
            1 => 'b',
            'row1_2' => 'b',
            2 => 'c',
            'row1_3' => 'c'
            ];
                
        $row1 = [
            ADODB_FETCH_NUM => $row1_1,
            ADODB_FETCH_ASSOC => $row1_2,
            ADODB_FETCH_BOTH => $row1_3,
        ];

        $row2_1 = ['123','234'];
        $row2_2 = ['row2_1' => '123', 'row2_2' => '234'];
        $row2_3 = [
            0 => '123',
            'row2_1' => '123',
            1 => '234',
            'row2_2' => '234'
            ];
                
        $row2 = [
            ADODB_FETCH_NUM => $row2_1,
            ADODB_FETCH_ASSOC => $row2_2,
            ADODB_FETCH_BOTH => $row2_3
        ];

        $row3_1 = ['1',null,'3',''];
        $row3_2 = ['row3_1' => '1', 'row3_2' => null, 'row3_3' => '3', 'row3_4' => ''];
        $row3_3 = [
            0 => '1',
            'row3_1' => '1',
            1 => null,
            'row3_2' => null,
            2 => '3',
            'row3_3' => '3',
            3 => '',
            'row3_4' => ''
        ];
                
        $row3 = [
            ADODB_FETCH_NUM => $row3_1,
            ADODB_FETCH_ASSOC => $row3_2,
            ADODB_FETCH_BOTH => $row3_3,
        ];



        
        $loadProcedure = sprintf(
            '%s/DatabaseSetup/%s/mysqli-multiquery-test.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        readSqlIntoDatabase($this->db, $loadProcedure);

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {

            $absoluteMode = $this->insertFetchMode($fetchMode);
            
            $re = $this->db->execute('CALL adodb_test_multi_recordsets()');
            $row = $re->fetchRow();
            
            $this->assertSame(
                $row1[$absoluteMode],
                $row,
                sprintf(
                    '[FETCH %s] Row 1 shold return matching object',
                    $fetchDescription
                )
            );
            
            //print_r($row);
            $re->nextRecordSet();
            $row = $re->fetchRow();
            
            $this->assertSame(
                $row2[$absoluteMode],
                $row,
                sprintf(
                    '[FETCH %s] Row 2 shold return matching object',
                    $fetchDescription
                )
            );
            
            //print_r($row);
            $re->nextRecordSet();
            $row = $re->fetchRow();
            //print_r($row);
            
            $this->assertSame(
                $row3[$absoluteMode],
                $row,
                sprintf(
                    '[FETCH %s] Row 3 shold return matching object',
                    $fetchDescription
                )
            );
            
            $re->close();
        }

    }

    
    /**
     * Data provider for {@see testMultiQueryWithStoredProcedure()}
     *
     * @return array [int fetchmode, array expected result, string sql, ?array bind]
     */

    public static function providerMultiQueryWithStoredProcedure(): array
    {

    }
    /*
    DOFetchObj Object
(
    [0] => a
    [1] => b
    [2] => c
)
ADOFetchObj Object
(
    [0] => 123
    [1] => 234
)
ADOFetchObj Object
(
    [0] => 1
    [1] => 
    [2] => 3
    [3] => 
)
------------------------------------------------------------------------------
mysqli: DROP PROCEDURE IF EXISTS adodb_test_multi_recordsets
------------------------------------------------------------------------------
------------------------------------------------------------------------------
mysqli: CREATE PROCEDURE adodb_test_multi_recordsets() LANGUAGE SQL NOT DETERMINISTIC SQL SECURITY DEFINER BEGIN SELECT "a" row1_1, "b" row1_2, "c" row1_3; SELECT "123" row2_1, "234" row2_2; SELECT 1 row3_1, null row3_2, 3 row3_3, '' row3_4; END; 
------------------------------------------------------------------------------
------------------------------------------------------------------------------
mysqli: CALL adodb_test_multi_recordsets()
------------------------------------------------------------------------------
ADOFetchObj Object
(
    [row1_1] => a
    [row1_2] => b
    [row1_3] => c
)
ADOFetchObj Object
(
    [row2_1] => 123
    [row2_2] => 234
)
ADOFetchObj Object
(
    [row3_1] => 1
    [row3_2] => 
    [row3_3] => 3
    [row3_4] => 
)
------------------------------------------------------------------------------
mysqli: DROP PROCEDURE IF EXISTS adodb_test_multi_recordsets
------------------------------------------------------------------------------
------------------------------------------------------------------------------
mysqli: CREATE PROCEDURE adodb_test_multi_recordsets() LANGUAGE SQL NOT DETERMINISTIC SQL SECURITY DEFINER BEGIN SELECT "a" row1_1, "b" row1_2, "c" row1_3; SELECT "123" row2_1, "234" row2_2; SELECT 1 row3_1, null row3_2, 3 row3_3, '' row3_4; END; 
------------------------------------------------------------------------------
------------------------------------------------------------------------------
mysqli: CALL adodb_test_multi_recordsets()
------------------------------------------------------------------------------
ADOFetchObj Object
(
    [0] => a
    [row1_1] => a
    [1] => b
    [row1_2] => b
    [2] => c
    [row1_3] => c
)
ADOFetchObj Object
(
    [0] => 123
    [row2_1] => 123
    [1] => 234
    [row2_2] => 234
)
ADOFetchObj Object
(
    [0] => 1
    [row3_1] => 1
    [1] => 
    [row3_2] => 
    [2] => 3
    [row3_3] => 3
    [3] => 
    [row3_4] => 
)
    */
}
