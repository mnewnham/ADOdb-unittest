<?php

/**
 * Tests cases for variables and constants of ADODb
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

namespace MNewnham\ADOdbUnitTest;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class VariablesTest extends ADOdbTestCase
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

        $db->startTrans();
        $db->execute(sprintf(
            'DROP TABLE IF EXISTS %s',
            _adodb_quote_fieldname($db, 'select')
            )
        );
        $db->completeTrans();

        $db->startTrans();
        $ok = readSqlIntoDatabase($db, $schemaFile);
        $db->completeTrans();
    }


    /**
     * Tests if the isConnected method works
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:isconnected
     *
     * @return void
     */
    public function testIsConnected(): void
    {
        $isConnected = $this->db->isConnected();

        $this->assertSame(
            true,
            $isConnected,
            'A connected database should return true from the isConnected() method'
        );
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

        list($template, $errno, $errmsg) = $this->executeSqlString($sql);

        $ar = array(
            'column_name' => 'Sample data'
        );

        $sql = $this->db->getInsertSQL(
            $template,
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
            'Data insertion should have succeeded using Quoted field and table names and added at least one record'
        );


    }

    /**
     * Test for {@see $ADODB_FETCH_MODE}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_fetch_mode
     *
     * @return void
     */
    public function testFetchMode(): void
    {
        global $ADODB_FETCH_MODE;

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

    /**
     * Test for {@see $ADODB_GETONE_EOF}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_getone_eof
     *
     * @return void
     */
    public function testGetOneEof(): void
    {
        global $ADODB_GETONE_EOF;

        $sql = 'select varchar_field from testtable_1 where id=9999';
        $test = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            $test,
            false,
            'getOne by default should return false when no row is found'
        );

        $ADODB_GETONE_EOF = -1;

        $test = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            $test,
            -1,
            'getOne should now flag by -1 when no row is found'
        );
    }
    /**
     * Test for {@see $ADODB_COUNTRECS}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_countrecs
     *
     * @return void
     */
    public function testCountRecsUsingCountRecsFalse(): void
    {
        global $ADODB_COUNTRECS;
        $ADODB_COUNTRECS = false; // Set to true by default

        $sql = 'select varchar_field from testtable_1 where id<9999';

        list($result, $errno, $errmsg) = $this->executeSqlString($sql);

        $this->assertEquals(
            -1,
            $result->recordCount(),
            'With ADODB_COUNTRECS set to false, the record count should be -1'
        );

        $ADODB_COUNTRECS = true; // Reset to default for other tests
    }

    /**
     * Test for {@see $ADODB_COUNTRECS}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_countrecs
     *
     * @return void
     */
    public function testCountRecsUsingCountRecsTrue(): void
    {
        global $ADODB_COUNTRECS;
        $ADODB_COUNTRECS = true; // Set to true by default

        $sql = "SELECT COUNT(*) FROM testtable_3";

        $this->db->startTrans();
        $countedRecords = $this->db->getOne($sql);
        $this->db->completeTrans();


        $sql = "SELECT * FROM testtable_3";

        list($result, $errno, $errmsg) = $this->executeSqlString($sql);

        $this->assertEquals(
            $countedRecords,
            $result->recordCount(),
            'With ADODB_COUNTRECS set to true, the record count should be ' . $countedRecords
        );

        $result->fetchRow();
    }

    /**
     * Tests the charMax() method
     *
     * @return void
     */
    public function testCharMax(): void
    {

        $value = $this->db->charMax();

        $this->assertIsInt(
            $value,
            'charMax() should return an integer value'
        );
    }

    /**
     * Tests the textMax() method
     *
     * @return void
     */
    public function testTextMax(): void
    {

        $value = $this->db->textMax();

        $this->assertIsInt(
            $value,
            'textMax() should return an integer value'
        );
    }
}
