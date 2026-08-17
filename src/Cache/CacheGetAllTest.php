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
class CacheGetAllTest extends CacheFunctions
{
    
    /**
     * Test for {@see ADODConnection::cachegetAll()}
     *
     * @param int    $fetchMode     Fetch mode to use
     * @param array  $expectedValue Expected value of the result
     * @param string $sql           SQL query to execute
     * @param ?array $bind          Optional array of bind parameters
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:cachegetall
     */
    #[DataProvider('providerTestCacheGetAll')]
    public function testCacheGetAll(
        int $fetchMode,
        array $expectedValue,
        string $sql,
        ?array $bind
    ): void {

        if ($this->skipAllTests) {
            $this->markTestSkipped('Skipping tests as caching not configured');
            return;
        }

        global $ADODB_CACHE_DIR;

        $this->db->setFetchMode($fetchMode);

        if ($bind) {
            $returnedRows = $this->db->cacheGetAll($this->timeout, $sql, $bind);
        } else {
            $returnedRows = $this->db->cacheGetAll($this->timeout, $sql);
        }

        list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);

        if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            foreach ($expectedValue as $ek => $er) {
                $er = array_change_key_case($er, CASE_UPPER);
                $expectedValue[$ek] = $er;
            }
        }

        $this->assertSame(
            $expectedValue,
            $returnedRows,
            sprintf(
                "Initial read of cacheGetAll() with FETCH MODE %s
                and casing %s returns %s",
                $fetchMode,
                ADODB_ASSOC_CASE,
                print_r($returnedRows, true)
            )
        );

        /*
        * This changes the value of the varchar_field in the database
        * but the cache should still return the original value
        */
        $rewriteSql = "UPDATE testtable_3 
                          SET varchar_field = 'SOME OTHER VALUE'
                        WHERE number_run_field = 3";

        list($result, $errrno, $errmsg) = $this->executeSqlString($rewriteSql);

        if ($bind) {
            $returnedRows = $this->db->cacheGetAll($this->timeout, $sql, $bind);
        } else {
            $returnedRows = $this->db->cacheGetAll($this->timeout, $sql);
        }

        list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);

        $this->assertSame(
            $expectedValue,
            $returnedRows,
            'Second read of cacheGetAll should return cache not current()'
        );

         $rewriteSql = "UPDATE testtable_3 
                          SET varchar_field = 'LINE 3' 
                        WHERE number_run_field = 3";

        list($result, $errrno, $errmsg) = $this->executeSqlString($rewriteSql);
    }

    /**
     * Data provider for {@see testGetAll()}
     *
     * @return array [int fetchode, array return value, string sql, ?array bind]
     */
    public static function providerTestCacheGetAll(): array
    {
        $GLOBALS['ADOdbConnection']->param(false);
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $p2 = $GLOBALS['ADOdbConnection']->param('p2');
        $bind = array('p1' => 2,
                      'p2' => 6
                    );
        return [
            'Numbers Between 2 and 6,Unbound, FETCH_ASSOC' =>
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
                        array('0' => 'LINE 2'),
                        array('0' => 'LINE 3'),
                        array('0' => 'LINE 4'),
                        array('0' => 'LINE 5'),
                        array('0' => 'LINE 6')
                        ),
                    "SELECT testtable_3.varchar_field 
                       FROM testtable_3
                        WHERE number_run_field BETWEEN $p1 AND $p2 
                     ORDER BY number_run_field", $bind],

                ];
    }

}
