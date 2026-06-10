<?php

/**
 * Tests cases for Cache SQL functions used as the core function of ADODb
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

namespace MNewnham\ADOdbUnitTest\CoreModule;

use MNewnham\ADOdbUnitTest\CoreModule\ADOdbCoreSetup;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb Core functions
 */
class CacheAsGetRowTest extends ADOdbCoreSetup
{
    /**
     * Test for {@see ADODConnection::getRow()]
     *
     * @param int    $expectedValue The value to return
     * @param string $sql           The SQL to execute
     * @param ?array $bind          Optional Bind
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:getrow
     */
    #[DataProvider('providerTestCacheAsGetRow')]
    public function testCacheAsGetRow(int $expectedValue, string $sql, ?array $bind): void
    {

        if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            $fields = [ '0' => 'ID',
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
            $fields = [ '0' => 'id',
                        '1' => 'varchar_field',
                        '2' => 'datetime_field',
                        '3' => 'date_field',
                        '4' => 'integer_field',
                        '5' => 'decimal_field',
                        '6' => 'boolean_field',
                        '7' => 'empty_field',
                        '8' => 'number_run_field'
                      ];
        }

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->insertFetchMode($fetchMode);



            if ($bind == null) {
                $record = $this->db->cacheGetRow($sql);
            } else {
                $record = $this->db->cacheGetRow($sql, $bind);
            }

            list($errno,$errmsg) = $this->assertADOdbError($sql, $bind);



            if ($expectedValue == 1) {
                switch ($fetchMode) {
                    case 1:
                    case 4:
                        foreach ($fields as $key => $value) {
                            $this->assertArrayHasKey(
                                $value,
                                $record,
                                sprintf(
                                    '[%s] Checking if associative key exists in fields array',
                                    $fetchDescription
                                )
                            );
                        }
                        break;
                    case 0:
                    case 3:
                        foreach ($fields as $key => $value) {
                            $this->assertArrayHasKey(
                                $key,
                                $record,
                                sprintf(
                                    '[%s] Checking if numeric key exists in fields array',
                                    $fetchDescription
                                )
                            );
                        }
                        break;
                    case 2:
                    case 5:
                        foreach ($fields as $key => $value) {
                            $this->assertArrayHasKey(
                                $value,
                                $record,
                                sprintf(
                                    '[%s] Checking if associative key ' .
                                    'exists in fields array',
                                    $fetchDescription
                                )
                            );
                        }

                        foreach ($fields as $key => $value) {
                            $this->assertArrayHasKey(
                                $key,
                                $record,
                                sprintf(
                                    '[%s] Checking if numeric key ' .
                                    'exists in fields array',
                                    $fetchDescription
                                )
                            );
                        }
                        break;
                }
            } else {
                $this->assertSame(
                    $record,
                    array(),
                    'Out of range record for getRow() should return empty array'
                );
            }
        }
    }

    /**
     * Data provider for {@see testGetRow()}
     *
     * @return array [int numOfRows, string sql, ?array bind]
     */
    public static function providerTestCacheAsGetRow(): array
    {

        $GLOBALS['ADOdbConnection']->param(false);
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 11);

        return [
            [
                1,
                "SELECT * 
                   FROM testtable_3 
               ORDER BY number_run_field",
               null
            ],[
                1,
                "SELECT * 
                   FROM testtable_3 
                  WHERE number_run_field=$p1",
                array('p1' => 11)
            ],[
                0,
                "SELECT * 
                   FROM testtable_3 
                  WHERE number_run_field=$p1",
                array('p1' => -999)
            ],
        ];
    }
}
