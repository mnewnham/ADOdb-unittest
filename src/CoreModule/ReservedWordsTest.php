<?php

/**
 * Tests cases for Reserved words in SQL statements of ADODb
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

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class ReservedWordsTest extends ADOdbTestCase
{
    protected ?object $db;
    protected ?string $adoDriver;
    protected ?object $dataDictionary;

    protected bool $skipFollowingTests = false;

    protected string $testTableName = 'select';
    protected string $testIdColumnName = 'ID';


    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        $db = $GLOBALS['ADOdbConnection'];
        /*
        * Load the table to test data length tests
        */
        $schemaFile = sprintf(
            '%s/DatabaseSetup/%s/reserved-words-test.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        if ($GLOBALS['DriverControl']->supportsDropIfExists) {
            if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
                $db->startTrans();
            }   
            
            $db->execute(sprintf(
                'DROP TABLE IF EXISTS %s',
                _adodb_quote_fieldname($db, 'select')
            ));

            if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
                $db->completeTrans();
            }
        }

        /*
        *load Data into the table, checking for driver specific loader
        */

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->startTrans();
        }
        $ok = readSqlIntoDatabase($db, $schemaFile);
        /*
        *load Data into the table, checking for driver specific loader
        */

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->completeTrans();
        }
    }

    public function setup(): void
    {
        parent::setup();
    }

   
    /**
     * Test for {@see $ADODB_QUOTE_FIELDNAMES}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_quote_fieldnames
     *
     * @return void
     */
    public function testQuotingExecute(): void
    {

        global $ADODB_QUOTE_FIELDNAMES;

        $ADODB_QUOTE_FIELDNAMES = false;
        /*
        * Fetch a template row from the table
        */

        $sql = sprintf(
            "SELECT * FROM %s WHERE %s=-1",
            _adodb_quote_fieldname($this->db, $this->testTableName),
            _adodb_quote_fieldname($this->db, $this->testIdColumnName)
        );

        list($template, $errno, $errmsg) = $this->executeSqlString($sql, null, false);

        $ar = array(
            'column_name' => 'Sample data'
        );

        $sql = $this->db->getInsertSQL(
           $this->testTableName,
            $ar
        );

        list($errno, $errmsg) = $this->assertADOdbError($sql);

        list($response, $errno, $errmsg) = $this->executeSqlString($sql);

        $success = is_object($response);

        $this->assertSame(
            true,
            $success,
            'Data insertion should succeed using quoted field and table reserved names'
        );

        $sql = sprintf(
            "SELECT COUNT(*) FROM %s",
            _adodb_quote_fieldname($this->db, $this->testTableName)
        );

        $count = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertGreaterThan(
            0,
            $count,
            'Data insertion should have succeeded using Quoted field ' .
            'and table names and added at least one record'
        );
    }

    /**
     * Test for {@see $ADODB_FETCH_MODE}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_fetch_mode
     *
     * @return void
     */
    public function testFetchModeWithReservedWords(): void
    {
        global $ADODB_FETCH_MODE;
        $expectedResult = '';

        $caseDescription = 'NOT SET';
        switch (ADODB_ASSOC_CASE) {
            case ADODB_ASSOC_CASE_UPPER:
                $expectedResult = 'ID';
                $caseDescription = 'ADODB_ASSOC_CASE_UPPER';
                break;
            case ADODB_ASSOC_CASE_LOWER:
                $caseDescription = 'ADODB_ASSOC_CASE_LOWER';
                $expectedResult = 'id';
                break;

            case ADODB_ASSOC_CASE_NATIVE:
                $expectedResult = 'id';
                $caseDescription = 'ADODB_ASSOC_CASE_NATIVE';
                break;
        }

        $fetchMode = $ADODB_FETCH_MODE;
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);

        /*
        * Fetch a template row from the table
        */
        $sql = sprintf(
            "SELECT * FROM %s",
            _adodb_quote_fieldname($this->db, $this->testTableName)
        );

        $testRow = $this->db->getRow($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertArrayHasKey(
            $expectedResult,
            $testRow,
            sprintf(
                "With casing set to %s and fetch mode set to ADODB_FETCH_ASSOC,\n" .
                "row should have an [%s] column",
                $caseDescription,
                $expectedResult
            )
        );

        // Cannot set the fetch mode to ADODB_FETCH_NUM this way
        //$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        // must do it through the db object
        $this->db->setFetchMode(ADODB_FETCH_NUM);

        $testRow = $this->db->getRow($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $expectedResult = '0'; // Numeric index for the first column

        $this->assertArrayHasKey(
            $expectedResult,
            $testRow,
            sprintf(
                "With casing set to %s and fetch mode set to ADODB_FETCH_NUM,\n" .
                "row should have an array index [%s] column",
                $caseDescription,
                $expectedResult
            )
        );

        $this->db->setFetchMode(ADODB_FETCH_BOTH);

        $testRow = $this->db->getRow($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $expectedResult = '0'; // Numeric index for the first column

        $this->assertArrayHasKey(
            $expectedResult,
            $testRow,
            sprintf(
                "With casing set to %s and fetch mode set to ADODB_FETCH_BOTH,\n" .
                "row should have an array index [%s] column",
                $caseDescription,
                $expectedResult
            )
        );

        $this->assertArrayHasKey(
            $expectedResult,
            $testRow,
            sprintf(
                "With casing set to %s and fetch mode set to ADODB_FETCH_BOTH,\n" .
                "row should also have an [%s] column",
                $caseDescription,
                $expectedResult
            )
        );

        $this->db->setFetchMode($fetchMode);
    }
}
