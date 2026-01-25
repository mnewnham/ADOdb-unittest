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
 * @copyright 2025 Mark Newnham, Damien Regad and the ADOdb community
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 *
 * @link https://github.com/mnewnham/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

namespace MNewnham\ADOdbUnitTest\Helpers;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class GetInsertSqlTest
 *
 * Test cases for for ADOdb getInsertSql
 */
class GetInsertSqlTest extends ADOdbTestCase
{
    protected string $testTableName = 'testtable_3';

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
    public function testGetInsertSqlWithObjectAndValidArray(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->db->setFetchMode($fetchMode);

            $sql = "SELECT * FROM {$this->testTableName} ORDER BY id DESC";
            $lastRecord = $this->db->getRow($sql);

            $sql = "SELECT * FROM {$this->testTableName} WHERE id=-1";

            list ($template,$errno,$errmsg) = $this->executeSqlString($sql);

            $ar = array(
                'varchar_field' => $this->db->qStr("GETINSERTSQL'0") . $fetchMode,
                'integer_field' => 99,
                'number_run_field' => 3001 + $fetchMode
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


            $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

            $this->assertTrue(
                $ok,
                'getInsertSql should return an ADORecordSet_empty object ' .
                'If the record is created successfully'
            );

            $sql = "SELECT * FROM {$this->testTableName} ORDER BY id DESC";
            $newRecord = $this->db->getRow($sql);

            if ($fetchMode == ADODB_FETCH_NUM) {
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



            /*
            id INT NOT NULL AUTO_INCREMENT,
            varchar_field VARCHAR(20),
            datetime_field DATETIME,
            date_field DATE,
            integer_field INT(2) DEFAULT 0,
            decimal_field decimal(12.2) DEFAULT 0,
            boolean_field BOOLEAN DEFAULT 0,
            empty_field VARCHAR(240) DEFAULT '',
            number_run_field INT(4) DEFAULT 0,
            */
        }
    }

    /**
     * Test for {@see ADODConnection::getInsertSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getinsertsql
     *
     * @return void
     */
    public function testGetInsertSqlWithStringAndValidArray(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->db->setFetchMode($fetchMode);

            $sql = "SELECT * FROM {$this->testTableName} ORDER BY id DESC";
            $lastRecord = $this->db->getRow($sql);



            $ar = array(
                'varchar_field' => 'GETINSERTSQL\'1' . $fetchMode,
                'integer_field' => 98,
                'number_run_field' => 3011 + $fetchMode
            );

            /*
            * This should create a record populated with default values and the
            * next available id
            */

            $sql = $this->db->getInsertSql($this->testTableName, $ar);

            $response = $this->db->execute($sql);

            $this->assertIsObject(
                $response,
                'insertion should return an object ' .
                'If the record is created successfully'
            );


            $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

            $this->assertTrue(
                $ok,
                'getInsertSql should return an ADORecordSet_empty object ' .
                'If the record is created successfully'
            );

            $sql = "SELECT * FROM {$this->testTableName} ORDER BY id DESC";
            $newRecord = $this->db->getRow($sql);

            if ($fetchMode == ADODB_FETCH_NUM) {
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

    /**
     * Test for {@see ADODConnection::getInsertSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getinsertsql
     *
     * @return void
     */
    public function testGetInsertSqlWithObjectAndInvalidArray(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->db->setFetchMode($fetchMode);

            $sql = "SELECT * FROM {$this->testTableName} ORDER BY id DESC";
            $lastRecord = $this->db->getRow($sql);

            $sql = "SELECT * FROM {$this->testTableName} WHERE id=-1";

            list ($template,$errno,$errmsg) = $this->executeSqlString($sql);

            $ar = array(
                'varchar_field' => 'GETINSERTSQL\'2' . $fetchMode,
                'integer_field' => 99,
                'number_run_field' => 3021 + $fetchMode,
                'some_invalid_field' => 'ABC123'
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


            $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

            $this->assertTrue(
                $ok,
                sprintf(
                    '[%s] getInsertSql should return an ADORecordSet_empty object ' .
                    'If the record is created successfully',
                    $fetchDescription
                )
            );

            $sql = "SELECT * FROM {$this->testTableName} ORDER BY id DESC";
            $newRecord = $this->db->getRow($sql);

            if ($fetchMode == ADODB_FETCH_NUM) {
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

    /**
     * Test for {@see ADODConnection::getInsertSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getinsertsql
     *
     * @return void
     */
    public function testGetInsertSqlWithStringAndInvalidArray(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->db->setFetchMode($fetchMode);

            $sql = "SELECT * FROM {$this->testTableName} ORDER BY id DESC";
            $lastRecord = $this->db->getRow($sql);

            $sql = "SELECT * FROM {$this->testTableName} WHERE id=-1";

            list ($template,$errno,$errmsg) = $this->executeSqlString($sql);

            $ar = array(
                'varchar_field' => 'GETINSERTSQL\'4' . $fetchMode,
                'integer_field' => 99,
                'number_run_field' => 3041 + $fetchMode,
                'some_invalid_field' => 'ABC123'
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

            $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

            $this->assertTrue(
                $ok,
                sprintf(
                    '[%s] getInsertSql should return an ADORecordSet_empty object ' .
                    'If the record is created successfully',
                    $fetchDescription
                )
            );

            $sql = "SELECT * FROM {$this->testTableName} ORDER BY id DESC";
            $newRecord = $this->db->getRow($sql);

            if ($fetchMode == ADODB_FETCH_NUM) {
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
}
