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
 * Class ZCacheFlushTest. Alphabetically last to prevent
 * conflicts with other cache tests
 *
 * Test cases for for ADOdb Caching
 */
class ZcacheFlushTest extends CacheFunctions
{
    
    /**
     * Test for {@see ADODConnection::cacheFlush()} flushing a single table
     * using bind parameters
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:cacheFlush
     */
    public function testCacheFlushOneBound(): void
    {

        if ($this->skipAllTests) {
            $this->markTestSkipped('Skipping tests as caching not configured');
            return;
        }

        $GLOBALS['ADOdbConnection']->param(false);
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array(
            'p1' => 'LINE 11'
        );

        $sql = "SELECT testtable_3.varchar_field,testtable_3.* 
                  FROM testtable_3 WHERE varchar_field=$p1";

        $response = $this->db->cacheFlush($sql, $bind);

        $this->assertSame(
            null,
            $response,
            "CacheFlush should not return a value"
        );
    }

    /**
     * Test for {@see ADODConnection::cacheFlush()} flushing all tables
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:cacheFlush
     */
    public function testCacheFlushAll(): void
    {

        if ($this->skipAllTests) {
            $this->markTestSkipped('Skipping tests as caching not configured');
            return;
        }

        $response = $this->db->cacheFlush();

        $this->assertSame(
            true,
            $response,
            "CacheFlush All should return true"
        );
    }
}
