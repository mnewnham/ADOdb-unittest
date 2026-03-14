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
 * Class AddIndexTestTest
 *
 * Test cases for for ADOdb DataDictAddIndexTest
 */
class AddIndexTest extends DataDictFunctions
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

        $metaIndexes = $this->dataDictionary->metaIndexes($this->testTableName);
        if (array_key_exists('string_test_index', $metaIndexes)) {
            $dropIndexSql = $this->dataDictionary->dropIndexSql(
                'string_test_index',
                $this->testTableName
            );

            list ($response,$errno,$errmsg) = $this->executeDictionaryAction($dropIndexSql);
        }

        $flds = "VARCHAR_FIELD, DATE_FIELD, INTEGER_FIELD";
        $indexOptions = array(
            'UNIQUE'
        );

        $sqlArray = $this->dataDictionary->createIndexSQL(
            'string_test_index',
            $this->testTableName,
            $flds,
            $indexOptions
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaIndexes = array_change_key_case(
            $this->db->metaIndexes($this->testTableName),
            CASE_UPPER
        );

        $this->assertArrayHasKey(
            'STRING_TEST_INDEX',
            $metaIndexes,
            'AddIndexSQL Using String For Fields should now ' .
            'contain index string_test_index'
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

         $metaIndexes = $this->dataDictionary->metaIndexes($this->testTableName);
        if (array_key_exists('array_test_index', $metaIndexes)) {
            $dropIndexSql = $this->dataDictionary->dropIndexSql(
                'array_test_index',
                $this->testTableName
            );

            list ($response,$errno,$errmsg) = $this->executeDictionaryAction($dropIndexSql);
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
            'array_test_index',
            $this->testTableName,
            $flds,
            $indexOptions
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $GLOBALS['baseTestsComplete'] = 2;

        $metaIndexes = array_change_key_case(
            $this->db->metaIndexes($this->testTableName),
            CASE_UPPER
        );

        $this->assertArrayHasKey(
            'ARRAY_TEST_INDEX',
            $metaIndexes,
            'AddIndexSQL Using Array For Fields should have ' .
            'added index array_test_index'
        );
    }
}
