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
 * Test cases for for Core Cache Execution
 */
class CacheExecuteTest extends CacheFunctions
{
    
    /**
     * Data provider for {@see testSelectExecute()}
     *
     * @return array [bool $success string $sql, ?array $bind]
     */
    public static function providerTestSelectCacheExecute(): array
    {

        $GLOBALS['ADOdbConnection']->param(false);
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 1);
        return [
            'Select Unbound' =>
                [true, "SELECT * FROM testtable_3 ORDER BY number_run_field", null],
            'Invalid' =>
                [false, "SELECT testtable_3.varchar_fieldx 
                           FROM testtable_3 
                       ORDER BY number_run_field",
                       null],
            'Select, Bound' =>
                [true, "SELECT testtable_3.varchar_field,testtable_3.* 
                         FROM testtable_3 
                        WHERE number_run_field=$p1", $bind],

            ];
    }

    /**
     * Test for {@see ADODConnection::execute() in select mode]
     *
     * @param bool   $expectedValue Expected value of the result
     * @param string $sql           SQL query to execute
     * @param ?array $bind          Optional array of bind parameters
     *
     * @return void
     *
     * @link         https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:cacheexecute
     */
    #[DataProvider('providerTestSelectCacheExecute')]
    public function testSelectCacheExecute(
        bool $expectedValue,
        string $sql,
        ?array $bind
    ): void {

        if ($this->skipAllTests) {
            $this->markTestSkipped('Skipping tests as caching not configured');
            return;
        }

        $expectedError = ($expectedValue == false) ? true : false;

        if ($bind) {
            $result = $this->db->cacheExecute($this->timeout, $sql, $bind);
        } else {
            $result = $this->db->cacheExecute($this->timeout, $sql);
        }
        list($errno, $errmsg) = $this->assertADOdbError($sql, $bind, $expectedError);

        $this->assertSame(
            $expectedValue,
            is_object($result),
            'First access of cacheExecute in SELECT mode sets cache'
        );

        if ($bind) {
            $result = $this->db->cacheExecute($this->timeout, $sql, $bind);
        } else {
            $result = $this->db->cacheExecute($this->timeout, $sql);
        }
        list($errno, $errmsg) = $this->assertADOdbError($sql, $bind, $expectedError);

        $this->assertSame(
            $expectedValue,
            is_object($result),
            'Second access of cacheexecute() in SELECT mode ' .
            'should read object from cache, not database'
        );
    }

    /**
     * Test for {@see ADODConnection::cacheexecute() in non-seelct mode]
     *
     * @param bool   $expectedValue Expected value of the result
     * @param string $sql           SQL query to execute
     * @param ?array $bind          Optional array of bind parameters
     *
     * @return void
     *
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:cacheexecute
     */
    #[DataProvider('providerTestNonSelectCacheExecute')]
    public function testNonSelectCacheExecute(bool $expectedValue, string $sql, ?array $bind): void
    {

        global $ADODB_CACHE_DIR;
        if ($this->skipAllTests) {
            $this->markTestSkipped('Skipping tests as caching not configured');
            return;
        }

        $expectedError = ($expectedValue == false) ? true : false;

        $this->db->startTrans();

        if ($bind) {
            $result = $this->db->cacheExecute($this->timeout, $sql, $bind);
        } else {
            $result = $this->db->cacheExecute($this->timeout, $sql);
        }

        $this->db->completeTrans();

        list($errno, $errmsg) = $this->assertADOdbError($sql, $bind, $expectedError);

        if (is_object($result)) {
            $reflection = new \ReflectionClass($result);
            $shortName  = $reflection->getShortName();
            $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

            $this->assertTrue(
                $ok,
                'ADOConnection::execute() in INSERT/UPDATE/DELETE ' .
                'mode should return an empty ADORecordSet object, returned ' . $shortName
            );
        }
    }

    /**
     * Data provider for {@see testNonSelectExecute()}
     *
     * @return array [string(getRe, array return value]
     */
    public static function providerTestNonSelectCacheExecute(): array
    {
        $GLOBALS['ADOdbConnection']->param(false);
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 'LINE 1');
        return [
             'Update Unbound' => [
                true,
                "UPDATE testtable_3 SET integer_field=2000 WHERE id=1",
                null
             ],
              'Invalid' => [
                false,
                "UPDATE testtable_3 SET xinteger_field=2000 WHERE id=1",
                 null
             ],
              'Select, Bound' =>  [
                true,
                "UPDATE testtable_3 SET integer_field=2000 WHERE varchar_field=$p1",
                 $bind
             ],
        ];
    }

}
