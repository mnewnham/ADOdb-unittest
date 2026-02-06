<?php

/**
 * Tests cases for DataDictAddIndexTest functions of ADODb
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
 * Class DataDictAddIndexTestTest
 *
 * Test cases for for ADOdb DataDictAddIndexTest
 */
class DataDictAddIndexTest extends DataDictFunctions
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

    /**
     * Test for {@see ADODConnection::createIndexSQL()} passing a string
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:createindexsql
     *
     * @return void
     */
    public function testaddIndexToBasicTableViaString(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table or column ' .
                'was not created successfully'
            );
            return;
        }


        $sql = "DROP TABLE IF EXISTS {$this->testIndexName1}";

        list ($response,$errno,$errmsg) = $this->executeSqlString($sql);

        $flds = "VARCHAR_FIELD, DATE_FIELD, INTEGER_FIELD";
        $indexOptions = array(
            'UNIQUE'
        );

        $sqlArray = $this->dataDictionary->createIndexSQL(
            $this->testIndexName1,
            $this->testTableName,
            $flds,
            $indexOptions
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaIndexes = $this->db->metaIndexes($this->testTableName);

        $this->assertArrayHasKey(
            $this->testIndexName1,
            $metaIndexes,
            'AddIndexSQL Using String For Fields should now ' .
            'contain index ' . $this->testIndexName1
        );
    }

    /**
     * Test for {@see ADODConnection::createIndexSQL()} passing an array
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:createindexsql
     *
     * @return void
     */
    public function testaddIndexToBasicTableViaArray(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table or ' .
                'column was not created successfully'
            );
            return;
        }

        $flds = array(
            "DATE_FIELD",
            "INTEGER_FIELD",
            "VARCHAR_FIELD"
        );
        $indexOptions = array(
            'UNIQUE'
        );

        $sqlArray = $this->dataDictionary->createIndexSQL(
            $this->testIndexName2,
            $this->testTableName,
            $flds,
            $indexOptions
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $GLOBALS['baseTestsComplete'] = 2;

        $metaIndexes = $this->db->metaIndexes($this->testTableName);

        $this->assertArrayHasKey(
            $this->testIndexName2,
            $metaIndexes,
            'AddIndexSQL Using Array For Fields should have ' .
            'added index ' . $this->testIndexName1
        );
    }

}
