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
class CacheGetRowTest extends CacheFunctions
{
    
    /**
     * Test for {@see ADODConnection::cachegetRow()]
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:cachegetrow
     *
     * @param int $expectedValue Expected value of the result
     * @param string $sql SQL query to execute
     * @param ?array $bind Optional array of bind parameters
     *
     * @return void
     *
     */
    #[DataProvider('providerTestCacheGetRow')]
    public function testCacheGetRow(
        int $expectedValue,
        string $emptyColumn,
        string $sql,
        ?array $bind
    ): void {

        global $ADODB_CACHE_DIR;

        if ($this->skipAllTests) {
            $this->markTestSkipped('Skipping tests as caching not configured');
            return;
        }

        /*
        * Set a value to cache
        */
        $this->setEmptyColumn('80111');

        if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            $fields = [
                '0' => 'ID',
                '1' => 'VARCHAR_FIELD',
                '2' => 'DATETIME_FIELD',
                '3' => 'DATE_FIELD',
                '4' => 'INTEGER_FIELD',
                '5' => 'DECIMAL_FIELD',
                '6' => 'BOOLEAN_FIELD',
                '7' => 'EMPTY_FIELD',
                '8' => 'NUMBER_RUN_FIELD'
            ];
        } else {
            $fields = [
                'id',
                'varchar_field',
                'datetime_field',
                'date_field',
                'integer_field',
                'decimal_field',
                'boolean_field',
                'empty_field',
                'number_run_field'
            ];
        }

        if ($bind != null) {
            $this->db->setFetchMode(ADODB_FETCH_ASSOC);

            $record = $this->db->cacheGetRow($this->timeout, $sql, $bind);

            list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);

            foreach ($fields as $key => $value) {
                $this->assertArrayHasKey(
                    $value,
                    $record,
                    'Checking if associative key exists in returned record'
                );
            }
        } else {
            $this->db->setFetchMode(ADODB_FETCH_NUM);
            $record = $this->db->cacheGetRow($this->timeout, $sql);

            list($errno, $errmsg) = $this->assertADOdbError($sql);

            foreach ($fields as $key => $value) {
                $this->assertArrayHasKey(
                    $key,
                    $record,
                    'Checking if numeric key exists in fields array'
                );
            }
        }

        /*
        * Now update the empty_field column of the database, but 
        * the cache should hold the original value of 80111
        */
        $this->setEmptyColumn(null);

        /*
        * Reread the cached row
        * and check that the empty_field column is 80111
        */
        if ($bind != null) {
            $this->db->setFetchMode(ADODB_FETCH_ASSOC);

            $record = $this->db->cacheGetRow($this->timeout, $sql, $bind);

            list($errno, $errmsg) = $this->assertADOdbError($sql, $bind);
            foreach ($fields as $key => $value) {
                $this->assertArrayHasKey(
                    $value,
                    $record,
                    'Checking if associative key exists in returned record'
                );
            }

            $this->assertSame(
                '80111',
                $record[$emptyColumn],
                'Checking that empty_field column is read from cache as 80111'
            );
        } else {
            $this->db->setFetchMode(ADODB_FETCH_NUM);
            $record = $this->db->cacheGetRow($this->timeout, $sql);

            list($errno, $errmsg) = $this->assertADOdbError($sql);

            foreach ($fields as $key => $value) {
                $this->assertArrayHasKey(
                    $key,
                    $record,
                    'Checking if numeric key exists in fields array'
                );
            }

            $this->assertSame(
                '80111',
                $record[7],
                'Checking that empty_field column is read from cache as 80111'
            );
        }
        $this->skipAllTests = true;
    }

    /**
     * Data provider for {@see testCacheGetRow()}
     *
     * @return array [int success, string sql, ?array bind]
     */
    public static function providerTestCacheGetRow(): array
    {
        $GLOBALS['ADOdbConnection']->param(false);
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array(
            'p1' => 'LINE 11'
        );

        switch (ADODB_ASSOC_CASE) {
            case ADODB_ASSOC_CASE_UPPER:
                $firstColumn = 'EMPTY_FIELD';

                break;
            case ADODB_ASSOC_CASE_LOWER:
            default:
                $firstColumn = 'empty_field';

                break;
        }
        return [
                [
                    1,
                    $firstColumn,
                    "SELECT * FROM testtable_3 ORDER BY number_run_field DESC",
                    null
                ],[
                    1,
                    $firstColumn,
                    "SELECT * FROM testtable_3 WHERE varchar_field=$p1",
                    $bind
                ],
            ];
    }
}
