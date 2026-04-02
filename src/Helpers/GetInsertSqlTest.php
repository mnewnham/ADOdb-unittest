<?php

/**
 * Tests for the getInsertSql method
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

namespace MNewnham\ADOdbUnitTest\Helpers;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class GetInsertSqlTest
 *
 * Test cases for for ADOdb getInsertSql
 */
class GetInsertSqlTest extends ADOdbTestCase
{
    protected string $testTableName = 'testtable_3';

    /**
     * Set up the test environment first time
     *
     * @return void
     */
    public static function setupBeforeClass(): void
    {
        $db        = $GLOBALS['ADOdbConnection'];

        /*
        *load Data into the table, checking for driver specific loader
        */
         
        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions){
            $db->startTrans();
        }
        
        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/autoexecute-schema.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        /*
        * Loads the schema based on the DB type
        */
        readSqlIntoDatabase($db, $tableSchema);
        
         
        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions){
            $db->completeTrans();
        }
    }
    
    
    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();
    }

    /**
     * Test for {@see ADODConnection::getInsertSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getinsertsql
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testGetInsertSqlWithObjectAndValidArray(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $absoluteFetchMode = $this->insertFetchMode($fetchMode);

        $sql = "SELECT * FROM autoexecute ORDER BY id DESC";
        $lastRecord = $this->db->getRow($sql);

        $sql = "SELECT * FROM autoexecute WHERE id=-1";

        list ($template,$errno,$errmsg) = $this->executeSqlString($sql);

        $ar = array(
            'varchar_field' => "GETINSERTSQL'0", //$this->db->qStr("GETINSERTSQL'0") . $fetchMode,
            'integer_field' => 99,
            'number_run_field' => 3001 + $fetchMode,
            'date_field' => date('Y-m-d')
        );

        /*
        * This should create a record populated with default values and the
        * next available id
        */
        $sql = $this->db->getInsertSql($template, $ar);

        $response = $this->db->execute($sql);

        $this->assertIsObject(
            $response,
            'insertion should return an object ' .
            'If the record is created successfully'
        );

        if (is_object($response)) {
            $reflection = new \ReflectionClass($response);
            $shortName  = $reflection->getShortName();
            $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

            $this->assertTrue(
                $ok,
                'getInsertSql should return an empty ADORecordSet object ' .
                'If the record is updated successfully, returned ' . $shortName
            );
        }

        $sql = "SELECT * FROM autoexecute ORDER BY id DESC";
        $newRecord = $this->db->getRow($sql);

        if ($absoluteFetchMode == ADODB_FETCH_NUM) {
            $field = 0;
        } elseif (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            $field = 'ID';
        } else {
            $field = 'id';
        }

        $this->assertArrayHasKey(
            $field,
            $newRecord,
            sprintf(
                '[%s] New record should have an field index %s',
                $fetchDescription,
                $field
            )
        );

        if (count($lastRecord) > 0) {

            $this->assertNotEquals(
                $lastRecord[$field],
                $newRecord[$field],
                sprintf(
                    '[%s] getInsertSQL() should have advanced id counter',
                    $fetchDescription
                )
            );

        }

    }

    /**
     * Test for {@see ADODConnection::getInsertSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getinsertsql
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testGetInsertSqlWithStringAndValidArray(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $this->insertFetchMode($fetchMode);

        $sql = "SELECT * FROM autoexecute ORDER BY id DESC";
        $lastRecord = $this->db->getRow($sql);



        $ar = array(
            'varchar_field' => 'GETINSERTSQL\'1' . $fetchMode,
            'integer_field' => 98,
            'number_run_field' => 3011 + $fetchMode,
            'date_field' => date('Y-m-d')
        );

        /*
        * This should create a record populated with default values and the
        * next available id
        */

        $tableName = 'autoexecute';

        $sql = $this->db->getInsertSql($tableName, $ar);

        $response = $this->db->execute($sql);

        $this->assertIsObject(
            $response,
            'insertion should return an object ' .
            'If the record is created successfully'
        );

        if (is_object($response)) {
            $reflection = new \ReflectionClass($response);
            $shortName  = $reflection->getShortName();
            $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

            $this->assertTrue(
                $ok,
                'getInsertSql should return an empty ADORecordSet object ' .
                'If the record is updated successfully, returned ' . $shortName
            );
        }

        $sql = "SELECT * FROM autoexecute ORDER BY id DESC";
        $newRecord = $this->db->getRow($sql);

        if ($fetchMode == 0 || $fetchMode == 3) {
            $field = 0;
        } elseif (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            $field = 'ID';
        } else {
            $field = 'id';
        }

        $this->assertArrayHasKey(
            $field,
            $newRecord,
            sprintf(
                '[%s] New record should have an field index %s',
                $fetchDescription,
                $field
            )
        );

        $this->assertNotEquals(
            $lastRecord[$field],
            $newRecord[$field],
            sprintf(
                '[%s] getInsertSQL() should have advanced id counter',
                $fetchDescription
            )
        );

    }

    /**
     * Test for {@see ADODConnection::getInsertSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getinsertsql
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testGetInsertSqlWithObjectAndInvalidArray(
        int $fetchMode,
        string $fetchDescription
    ): void {

    
        $this->insertFetchMode($fetchMode);

        $sql = "SELECT * FROM autoexecute ORDER BY id DESC";
        $lastRecord = $this->db->getRow($sql);

        $sql = "SELECT * FROM autoexecute WHERE id=-1";

        list ($template,$errno,$errmsg) = $this->executeSqlString($sql);

        $ar = array(
            'varchar_field' => 'GETINSERTSQL\'2' . $fetchMode,
            'integer_field' => 99,
            'number_run_field' => 3021 + $fetchMode,
            'some_invalid_field' => 'ABC123',
            'datetime_field' => time(),
            'date_field' => date('Y-m-d')
        );

        /*
        * This should create a record populated with default values and the
        * next available id
        */

        $sql = $this->db->getInsertSql($template, $ar);

        $response = $this->db->execute($sql);

        $this->assertIsObject(
            $response,
            sprintf(
                '[%s] insertion should return an object ' .
                'If the invalid fields are discarded and ' .
                'the record is created successfully',
                $fetchDescription
            )
        );


        if (is_object($response)) {
            $reflection = new \ReflectionClass($response);
            $shortName  = $reflection->getShortName();
            $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

            $this->assertTrue(
                $ok,
                sprintf(
                    '[%s] getInsertSql should return an ADORecordSet_empty object ' .
                    'If the record is created successfully, returned: %s',
                    $fetchDescription,
                    $shortName
                )
            );
        }


        $sql = "SELECT * FROM autoexecute ORDER BY id DESC";
        $newRecord = $this->db->getRow($sql);

        if ($fetchMode == 0 || $fetchMode == 3) {
            $field = 0;
        } elseif (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            $field = 'ID';
        } else {
            $field = 'id';
        }

        $this->assertArrayHasKey(
            $field,
            $newRecord,
            sprintf(
                '[%s] New record should have an field index %s',
                $fetchDescription,
                $field
            )
        );

        $this->assertNotEquals(
            $lastRecord[$field],
            $newRecord[$field],
            sprintf(
                '[%s] getInsertSQL() should have advanced id counter',
                $fetchDescription
            )
        );
    }

    /**
     * Test for {@see ADODConnection::getInsertSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getinsertsql
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testGetInsertSqlWithStringAndInvalidArray(
        int $fetchMode,
        string $fetchDescription
    ): void {
    

        $this->insertFetchMode($fetchMode);

        $sql = "SELECT * FROM autoexecute ORDER BY id DESC";
        $lastRecord = $this->db->getRow($sql);

        $sql = "SELECT * FROM autoexecute WHERE id=-1";

        list ($template,$errno,$errmsg) = $this->executeSqlString($sql);

        $ar = array(
            'varchar_field' => 'GETINSERTSQL\'4' . $fetchMode,
            'integer_field' => 99,
            'number_run_field' => 3041 + $fetchMode,
            'some_invalid_field' => 'ABC123',
            'datetime_field' => time(),
            'date_field' => date('Y-m-d')
        );

        /*
        * This should create a record populated with default values and the
        * next available id
        */

        $sql = $this->db->getInsertSql($template, $ar);

        $response = $this->db->execute($sql);

        $this->assertIsObject(
            $response,
            sprintf(
                '[%s] insertion should return an object ' .
                'If the invalid fields are discarded and ' .
                'the record is created successfully',
                $fetchDescription
            )
        );

        if (is_object($response)) {
            $reflection = new \ReflectionClass($response);
            $shortName  = $reflection->getShortName();
            $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

            $this->assertTrue(
                $ok,
                sprintf(
                    '[%s] getInsertSql should return an ADORecordSet_empty object ' .
                    'If the record is created successfully, returned: %s',
                    $fetchDescription,
                    $shortName
                )
            );
        }

        $sql = "SELECT * FROM autoexecute ORDER BY id DESC";
        $newRecord = $this->db->getRow($sql);

        if ($fetchMode == 0 || $fetchMode == 3) {
            $field = 0;
        } elseif (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            $field = 'ID';
        } else {
            $field = 'id';
        }

        $this->assertArrayHasKey(
            $field,
            $newRecord,
            sprintf(
                '[%s] New record should have an field index %s',
                $fetchDescription,
                $field
            )
        );

        $this->assertNotEquals(
            $lastRecord[$field],
            $newRecord[$field],
            sprintf(
                '[%s] getInsertSQL() should have advanced id counter',
                $fetchDescription
            )
        );
    }
}
