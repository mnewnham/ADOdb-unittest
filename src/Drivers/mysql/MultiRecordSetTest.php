<?php

/**
 * Test the multi recordset stored procedure feature of the mysqli driver of ADOdb.
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

namespace MNewnham\ADOdbUnitTest\Drivers\mysql;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class MultiRecordSetTest
 *
 * Test cases for for the MySQLi multi-recordset feature
 */
class MultiRecordSetTest extends ADOdbTestCase
{
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

    }

    /**
     * Tests the multiQuery, stored procedure feature
     *
     * @return void
     */
    public function testMultiQueryWithStoredProcedure(): void
    {
        $this->db->multiQuery = true;

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
            
            $re->nextRecordSet();
            $row = $re->fetchRow();
            
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
}
