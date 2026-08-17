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
class CacheGetOneTest extends CacheFunctions
{
    
    /**
     * Test for {@see ADODConnection::cacheGetOne()]
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:cachegetone
     *
     * @param string $expectedValue Expected value of the result
     * @param string $sql SQL query to execute
     * @param ?array $bind Optional array of bind parameters
     *
     * @return void
     *
     */
     #[DataProvider('providerTestCacheGetOne')]
    public function testCacheGetOne(string $expectedValue, string $sql, ?array $bind): void
    {
        global $ADODB_CACHE_DIR;
        if ($this->skipAllTests) {
            $this->markTestSkipped('Skipping tests as caching not configured');
            return;
        }
        if ($bind) {
            $actualValue = $this->db->cacheGetOne($this->timeout, $sql, $bind);

            list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);
            $this->assertSame(
                $expectedValue,
                $actualValue,
                'First access of cacheGetOne() with bind ' .
                'reads from database and sets cache'
            );
        } else {
            $actualValue = $this->db->cacheGetOne($this->timeout, $sql);
            list($errno, $errmsg) = $this->assertADOdbError($sql);

            $this->assertSame(
                $expectedValue,
                $actualValue,
                'First access of cacheGetOne() reads from database and sets cache'
            );
        }

        $rewriteSql = "UPDATE testtable_3 
                          SET varchar_field = 'NOCACHE VALUE1' 
                        WHERE varchar_field = 'LINE 1'";

        list($result, $errno, $errmsg) = $this->executeSqlString($rewriteSql);

        if ($bind) {
            $actualValue = $this->db->cacheGetOne($this->timeout, $sql, $bind);

            list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);

            $this->assertSame(
                $expectedValue,
                $actualValue,
                'Second access of cacheGetOne() with bind should read ' .
                'from cache, not database'
            );
        } else {
            $actualValue = $this->db->cacheGetOne($this->timeout, $sql);

            list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);

            $this->assertSame(
                $expectedValue,
                $actualValue,
                'Second access of cacheGetOne() reads from cache, not database'
            );
        }
        $rewriteSql = "UPDATE testtable_3 
                          SET varchar_field = 'LINE 1' 
                        WHERE varchar_field = 'NOCACHE VALUE1'";

        list($result, $errno, $errmsg) = $this->executeSqlString($rewriteSql);
    }

    /**
     * Data provider for {@see testGetOne()}
     *
     * @return array [string(getRe, array return value]
     */
    public static function providerTestCacheGetOne(): array
    {
        $GLOBALS['ADOdbConnection']->param(false);
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 'LINE 11');

        return [
            'Return Last Col, Unbound' => [
                'LINE 11',
                "SELECT varchar_field FROM testtable_3 ORDER BY number_run_field DESC",
                null
            ],
            'Return Multiple Cols, take first, Unbound' => [
                'LINE 11',
                "SELECT testtable_3.varchar_field,testtable_3.* FROM testtable_3 ORDER BY number_run_field DESC",
                null
            ],
            'Return Multiple Cols, take first, Bound' => [
                'LINE 11',
                "SELECT testtable_3.varchar_field,testtable_3.* FROM testtable_3 WHERE varchar_field=$p1",
                $bind
            ],

        ];
    }

}
