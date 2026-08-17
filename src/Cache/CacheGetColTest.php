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
class CacheGetColTest extends CacheFunctions
{
    
    /**
     * Test for {@see ADODConnection::cachegetCol()]
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:cachegetcol
     *
     * @param int $expectedValue Expected value of the result
     * @param string $sql SQL query to execute
     * @param ?array $bind Optional array of bind parameters
     *
     * @return void
     *
     */
    #[DataProvider('providerTestCacheGetCol')]
    public function testGetCacheCol(int $expectedValue, string $sql, ?array $bind): void
    {
        global $ADODB_CACHE_DIR;
        if ($this->skipAllTests) {
            $this->markTestSkipped('Skipping tests as caching not configured');
            return;
        }
        if ($bind) {
            $cols = $this->db->cacheGetCol($sql, $bind);

            list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);

            $this->assertSame(
                $expectedValue,
                count($cols),
                'First access of cacheGetCol with bound variables() sets cache'
            );
        } else {
            $cols = $this->db->cacheGetCol($sql);

            list($errno, $errmsg) = $this->assertADOdbError($sql);

            $this->assertSame(
                $expectedValue,
                count($cols),
                'First access of cacheGetCol without bound variables() sets cache'
            );
        }

        $rewriteSql = "UPDATE testtable_3 
                          SET varchar_field = null
                        WHERE varchar_field = 'LINE 1'";
        $this->db->execute($rewriteSql);

        if ($bind) {
            $cols = $this->db->cacheGetCol($sql, $bind);

            list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);

            $this->assertSame(
                $expectedValue,
                count($cols),
                'Second access of cacheGetCol with bound variables() ' .
                'should read cache, not database'
            );
        } else {
            $cols = $this->db->cacheGetCol($sql);

            list($errno, $errmsg) = $this->assertADOdbError($sql);

            $this->assertSame(
                $expectedValue,
                count($cols),
                'Second access of cacheGetCol without bound variables() ' .
                'should read cache not database'
            );
        }
        $rewriteSql = "UPDATE testtable_3 
                          SET varchar_field = 'LINE 1' 
                        WHERE varchar_field = NULL";

        list($result, $errno, $errmsg) = $this->executeSqlString($rewriteSql);
    }

    /**
     * Data provider for {@see testCacheGetCol()}
     *
     * @return array [string(getRe, array return value]
     */
    public static function providerTestCacheGetCol(): array
    {
        $GLOBALS['ADOdbConnection']->param(false);
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 'LINE 11');
        return [
                [
                    11,
                    "SELECT varchar_field FROM testtable_3",
                    null
                ],[
                    1,
                    "SELECT testtable_3.varchar_field,testtable_3.* 
                       FROM testtable_3 WHERE varchar_field=$p1",
                    $bind
                ],

            ];
    }

}
