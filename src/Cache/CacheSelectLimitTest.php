<?php

/**
 * Tests cases for cache SQL functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\Cache;

use MNewnham\ADOdbUnitTest\Cache\CacheFunctions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class cacheSqlTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class CacheSelectLimitTest extends CacheFunctions
{
    
    /**
     * Test for {@see ADODConnection::cacheselectlimit() in select mode]
     *
     * @param int    $fetchMode     Fetch mode to use
     * @param array  $expectedValue Expected value of the result
     * @param string $sql           SQL query to execute
     * @param int    $rows          Number of rows to return
     * @param int    $offset        Offset to start returning rows from
     * @param ?array $bind          Optional array of bind parameters
     *
     * @return void
     *
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:cacheselectlimit
     *
     */
    #[DataProvider('providerTestCacheSelectLimit')]
    public function testCacheSelectLimit(
        int $fetchMode,
        array $expectedValue,
        string $sql,
        int $rows,
        int $offset,
        ?array $bind
    ): void {

        global $ADODB_CACHE_DIR;
        //global $ADODB_FETCH_MODE;
       
        if ($this->skipAllTests) {
            $this->markTestSkipped(
                'Skipping tests as caching not configured'
            );
            return;
        }

        $this->db->storeFetchModes();
        $this->db->setFetchMode($fetchMode);

        $this->db->startTrans();

        ///$this->db->debug = 2;

        if ($bind) {
          
            $result = $this->db->cacheSelectLimit(
                $this->timeout,
                $sql,
                $rows,
                $offset,
                $bind
            );
        } else {
           
            $result = $this->db->cacheSelectLimit(
                $this->timeout,
                $sql,
                $rows,
                $offset
            );
        }

        //list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);

        $returnedRows = array();
        while ($row = $result->fetchRow()) {
            $returnedRows[] = $row;
        }

        if ($fetchMode == ADODB_FETCH_BOTH) {
            if (is_array($returnedRows)) {
                $returnedRows  = $this->sortFetchBothRecords($returnedRows);
            }

            if (is_array($expectedValue)) {
                $expectedValue = $this->sortFetchBothRecords($expectedValue);
            }
              
        } 

        if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            foreach ($expectedValue as $ek => $er) {
                $er = array_change_key_case($er, CASE_UPPER);
                $expectedValue[$ek] = $er;
            }
        }

        $this->db->completeTrans();

        /*
        * This is the plan:
        * read record greater than 2 , so 3,4,5,6,7,8,9,10
        * add offet of 2, so 5,6,7,8,9,10
        * limit 4, so 5,6,5,8
        */ 

        $this->assertSame(
            $expectedValue,
            $returnedRows,
            sprintf(
                "Initial read of cacheSelectLimit() with FETCH MODE %s
                and casing %s using SQL %s returns %s, requires %s",
                $this->coreFetchModes[$fetchMode],
                $this->caseDescription[ADODB_ASSOC_CASE],
                $sql,
                print_r($returnedRows, true),
                print_r($expectedValue, true)
            )
        );

        $rewriteSql = "UPDATE testtable_3 
                          SET varchar_field = 'TCSL TEST VALUE' 
                        WHERE number_run_field = 3
                          AND varchar_field = 'LINE 3'";
        list($result, $errrno, $errmsg) = $this->executeSqlString($rewriteSql);

        $this->db->startTrans();

        if ($bind) {
            $result = $this->db->cacheSelectLimit(
                $this->timeout,
                $sql,
                $rows,
                $offset,
                $bind
            );
        } else {
            $result = $this->db->cacheSelectLimit(
                $this->timeout,
                $sql,
                $rows,
                $offset
            );
        }

        list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);

        $returnedRows = array();
        while ($row = $result->fetchRow()) {
            $returnedRows[] = $row;
        }

        if ($fetchMode == ADODB_FETCH_BOTH) {
            if (is_array($returnedRows)) {
                $returnedRows  = $this->validateFetchBothRecords($expectedValue, $returnedRows);
            }
        } 

        $this->db->completeTrans();

        $this->assertSame(
            $expectedValue,
            $returnedRows,
            sprintf(
                "Second read of cacheSelectLimit() with FETCH MODE %s
                and casing %s should read cache, not database but returns %s",
                $this->testFetchModes[$fetchMode],
                $this->caseDescription[ADODB_ASSOC_CASE],
                print_r($returnedRows, true)
            )
        );

        /*
        * Now rewrite the database back to its original state
        */
        $rewriteSql = "UPDATE testtable_3 
                          SET varchar_field = 'LINE 3' 
                        WHERE number_run_field = 3
                          AND varchar_field = 'TCSL TEST VALUE'";

        list($result, $errrno, $errmsg) = $this->executeSqlString($rewriteSql);

        $this->db->restoreFetchModes();
    }

    /**
     * Data provider for {@see testSelectLimit()}
     *
     * @return array [int $fetchMode, array $result, string $sql, int $offset, int $rows, ?array $bind]
     */
    public static function providerTestCacheSelectLimit(): array
    {
        $GLOBALS['ADOdbConnection']->param(false);
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');

        $bind = array(
            'p1' => '2'
        );

        return [
            'Select Unbound, FETCH_ASSOC' =>
                [ADODB_FETCH_ASSOC,
                    array(
                        array('varchar_field' => 'LINE 5'),
                        array('varchar_field' => 'LINE 6'),
                        array('varchar_field' => 'LINE 7'),
                        array('varchar_field' => 'LINE 8')
                    ),
                    "SELECT testtable_3.varchar_field 
                        FROM testtable_3 
                       WHERE number_run_field>2 
                    ORDER BY number_run_field",
                    4,
                    2,
                    null
                ],
            'Select, Bound, FETCH_NUM' => [
                ADODB_FETCH_NUM,
                array(
                    array('0' => 'LINE 5'),
                    array('0' => 'LINE 6'),
                    array('0' => 'LINE 7'),
                    array('0' => 'LINE 8')
                    ),
                "SELECT testtable_3.varchar_field 
                   FROM testtable_3 
                  WHERE number_run_field>$p1 
               ORDER BY number_run_field",
                4,
                2,
                $bind
            ],

             'Select, Bound, FETCH_BOTH' => [
                ADODB_FETCH_BOTH,
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
                  WHERE number_run_field>$p1 
               ORDER BY number_run_field",
                4,
                2,
                $bind
            ],

        ];
    }
}
