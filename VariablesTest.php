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
 * @copyright 2025 Mark Newnham, Damien Regad and the ADOdb community
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 * 
 * @link https://github.com/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

use PHPUnit\Framework\TestCase;

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

    protected string $testTableName = 'table_name';
    protected string $testIdColumnName = 'ID';

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

        $quotedTable = sprintf(
            '%s%s%s', 
            $this->db->nameQuote, 
            $this->testTableName,
            $this->db->nameQuote
        );

        
        $sql = "SELECT * FROM $quotedTable WHERE {$this->testIdColumnName}=-1";

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
            false,
            $success, 
            'Data insertion should not succeed using Unquoted field and table names'
        );

        $count = $this->db->getOne("SELECT COUNT(*) FROM $quotedTable");
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            0, 
            $count, 
            'Data insertion should not have succeeded using Unquoted field and table names'
        );
        
        /*
        * Now activate the quoting of field and table names
        */
        $ADODB_QUOTE_FIELDNAMES = true;

        $sql = $this->db->getInsertSQL(
            $template,
            $ar
        );
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        list($success, $errno, $errmsg) = $this->executeSqlString($sql);

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

        case ADODB_ASSOC_CASE_NATURAL:
            $expectedResult = 'id';
            $caseDescription = 'ADODB_ASSOC_CASE_NATURAL';
            break;

        }

        $fetchMode = $ADODB_FETCH_MODE;
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        
        /*
        * Fetch a template row from the table
        */
        $sql = "SELECT * FROM {$this->testTableName}";
        
        $testRow = $this->db->getRow($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertArrayHasKey(
            $expectedResult,
            $testRow,
            sprintf(
                "With casing set to %s and fetch mode set to ADODB_FETCH_ASSOC,\n" . 
                "row should have an [%s] column:\n actually %s",
                $caseDescription,
                $expectedResult,
                print_r($testRow, true)
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
                "row should have an array index [%s] column\n actually %s",
                $caseDescription,
                $expectedResult,
                print_r($testRow, true)
            )
        );
    
        $this->db->setFectchMode($fetchMode);
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
    public function testCountRecs(): void
    {   
        global $ADODB_COUNTRECS;
        $ADODB_COUNTRECS = true; // Set to true by default

 
        $sql = "SELECT * FROM testtable_3";
                
        list($result, $errno, $errmsg) = $this->executeSqlString($sql);

        $this->assertEquals(
            11,
            $result->recordCount(), 
            'With ADODB_COUNTRECS set to true, the record count should be 11'
        );        

        $ADODB_COUNTRECS = false;

        list($result, $errno, $errmsg) = $this->executeSqlString($sql);

        $this->assertEquals(
            -1,
            $result->recordCount(), 
            'With ADODB_COUNTRECS set to false, the record count should be -1'
        );

        $ADODB_COUNTRECS = true; // Reset to default for other tests
    }

}