<?php

/**
 * Tests cases for DataDictChangeTable functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\DataDict;

use MNewnham\ADOdbUnitTest\DataDict\DataDictFunctions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class DataDictChangeTableTest
 *
 * Test cases for for ADOdb DataDictChangeTable
 */
class DataDictChangeTableTest extends DataDictFunctions
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
    }

    /
    /**
     * Test for {@see ADODConnection::changeTableSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:changetablesql
     *
     * @return void
     */
    public function testChangeTable(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not ' .
                'created successfully'
            );
            return;
        }

        $flds = " 
            VARCHAR_FIELD VARCHAR(50) NOTNULL DEFAULT '',
            DATE_FIELD DATE NOTNULL DEFAULT '2010-01-01',
            ANOTHER_INTEGER_FIELD INTEGER NOTNULL DEFAULT 0,
            YET_ANOTHER_VARCHAR_FIELD VARCHAR(50) NOTNULL DEFAULT ''
            ";

        $sqlArray = $this->dataDictionary->changeTableSQL(
            $this->testTableName,
            $flds
        );

        $assertion = $this->assertIsArray(
            $sqlArray,
            'changeTableSql() should alway return an array'
        );

        if (!$assertion) {
            return;
        }

        if (count($sqlArray) == 0) {
            $this->fail(
                'changeTableSql() not supported by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }


        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayHasKey(
            'INTEGER_FIELD',
            $metaColumns,
            'changeTableSQL() using $dropflds=false ' .
            '- old column should be retained even if ' .
            'not in the new definition'
        );

        $this->assertArrayHasKey(
            'ANOTHER_INTEGER_FIELD',
            $metaColumns,
            'changeTableSql() ANOTHER_INTEGER_FIELD should have been added'
        );


        $this->assertArrayHasKey(
            'YET_ANOTHER_VARCHAR_FIELD',
            $metaColumns,
            'changeTableSQ() YET_ANOTHER_VARCHAR_FIELD should have been added'
        );

        if (!array_key_exists('ANOTHER_VARCHAR_FIELD', $metaColumns)) {
            $this->skipFollowingTests = true;
        }

        /*
        * Now re-execute wth the drop flag set to true
        */
        $sqlArray = $this->dataDictionary->changeTableSQL(
            $this->testTableName,
            $flds,
            false,
            true
        );


        $assertion = $this->assertIsArray(
            $sqlArray,
            'changeTableSql() should alway return an array'
        );

        if (!$assertion) {
            return;
        }

        if (count($sqlArray) == 0) {
            $this->fail(
                'changeTableSql() not supported by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayNotHasKey(
            'INTEGER_FIELD',
            $metaColumns,
            'changeTableSQL() using $dropFlds=true ' .
            'old column INTEGER_FIELD should be dropped'
        );
    }
}
