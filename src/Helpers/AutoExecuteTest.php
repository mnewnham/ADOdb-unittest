<?php

/**
 * Tests for the autoExecute method
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

use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class getUpdateSqlTest
 * Test cases for getUpdateSql
 */
class AutoExecuteTest extends ADOdbTestCase
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
     * Test for {@see ADODConnection::getUpdateSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getupdatesql
     *
     * @return void
     */
    public function testAutoExecuteInsert(): void
    {

        for ($forceMode = 0; $forceMode < 2; $forceMode++) {
            foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
                $this->db->setFetchMode($fetchMode);

                $aeVar = 'AUTOEXECUTE01' . $forceMode . $fetchMode;

                $ar = array(
                    'varchar_field' => $aeVar,
                    'integer_field' => 99,
                    'number_run_field' => 5001 + $fetchMode + (10 * $forceMode)
                );

                $response = $this->db->autoExecute($this->testTableName, $ar, 'INSERT');

                /*
                $this->assertIsObject(
                    $response,
                    'autoExecute insert should return an object ' .
                    'If the record is created successfully'
                );


                $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';
                */
                $this->assertTrue(
                    $response,
                    'autoExecute should return true ' .
                    'If the record is created successfully'
                );

                $sql = "SELECT varchar_field,integer_field FROM {$this->testTableName} ORDER BY id DESC";
                $newRecord = $this->db->getRow($sql);

                if ($fetchMode == ADODB_FETCH_NUM) {
                    $field = 0;
                } elseif (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
                    $field = 'VARCHAR_FIELD';
                } else {
                    $field = 'varchar_field';
                }

                $value = $newRecord[$field];

                $this->assertSame(
                    $aeVar,
                    $value,
                    sprintf(
                        '[%s] updated record should have an varchar_field value %s',
                        $fetchDescription,
                        'AUTOEXECUTE' . $forceMode . $fetchMode
                    )
                );
            }
        }
    }

    /**
     * Test for {@see ADODConnection::getUpdateSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getupdatesql
     *
     * @return void
     */
    public function testAutoExecuteUpdate(): void
    {

        $sql = "SELECT id FROM {$this->testTableName} ORDER BY id DESC";
        $lastId = $this->db->getOne($sql);

        $where = "id=$lastId";

        for ($forceMode = 0; $forceMode < 2; $forceMode++) {
            foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
                $aeVar = 'AUTOEXECUTE02' . $forceMode . $fetchMode;

                $this->db->setFetchMode($fetchMode);

                $ar = array(
                    'varchar_field' => $aeVar,
                    'integer_field' => 99,
                    'number_run_field' => 7001 + $fetchMode + (10 * ($forceMode + 1))
                );

                $response = $this->db->autoExecute(
                    $this->testTableName,
                    $ar,
                    'UPDATE',
                    $where,
                    $forceMode
                );



                /*
                $this->assertIsObject(
                    $response,
                    'autoExecute update should return an object ' .
                    'If the record is created successfully'
                );


                $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

                */
                $this->assertTrue(
                    $response,
                    'autoExecute should return true ' .
                    'If the record is updated successfully'
                );

                $sql = "SELECT varchar_field,integer_field FROM {$this->testTableName} ORDER BY id DESC";
                $newRecord = $this->db->getRow($sql);

                if ($fetchMode == ADODB_FETCH_NUM) {
                    $field = 0;
                } elseif (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
                    $field = 'VARCHAR_FIELD';
                } else {
                    $field = 'varchar_field';
                }

                $value = $newRecord[$field];

                $this->assertSame(
                    $aeVar,
                    $value,
                    sprintf(
                        '[%s] updated record should have an varchar_field value %s',
                        $fetchDescription,
                        'AUTOEXECUTE' . $forceMode . $fetchMode
                    )
                );
            }
        }
    }

    /**
     * Test for {@see ADODConnection::getUpdateSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getupdatesql
     *
     * @return void
     */
    public function testAutoExecuteUpdateQuoteFieldNames(): void
    {

        global $ADODB_QUOTE_FIELDNAMES;

        $qfArray = array(true,false,'BRACKETS','UPPER','LOWER');

        foreach ($qfArray as $qfIndex => $qfValue) {
            $ADODB_QUOTE_FIELDNAMES = $qfIndex;

            $sql = "SELECT id FROM {$this->testTableName} ORDER BY id DESC";
            $lastId = $this->db->getOne($sql);

            $where = "id=$lastId";

            for ($forceMode = 0; $forceMode < 2; $forceMode++) {
                foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
                    $this->db->setFetchMode($fetchMode);

                    $aeVar = 'AUTOEXECUTE03' . $forceMode . $fetchMode . $qfIndex;

                    $nrf = sprintf('8%s01%s%s', $qfIndex, $fetchMode, $forceMode + 1);

                    $ar = array(
                        'varchar_field' => $aeVar,
                        'integer_field' => 99,
                        'number_run_field' => $nrf
                    );

                    $response = $this->db->autoExecute(
                        $this->testTableName,
                        $ar,
                        'UPDATE',
                        $where,
                        $forceMode
                    );

                    /*
                    * @todo I think this should return an ADORecordset, not true
                    *
                    $this->assertIsObject(
                        $response,
                        'autoExecute update should return an object ' .
                        'If the record is created successfully'
                    );


                    $ok = is_object($response)
                        && get_class($response) == 'ADORecordSet_empty';
                    */
                    $this->assertTrue(
                        $response,
                        'autoExecute should return true ' .
                        'If the record is updated successfully'
                    );

                    $sql = "SELECT varchar_field,integer_field 
                            FROM {$this->testTableName} 
                        ORDER BY id DESC";


                    $newRecord = $this->db->getRow($sql);

                    if ($fetchMode == ADODB_FETCH_NUM) {
                        $field = 0;
                    } elseif (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
                        $field = 'VARCHAR_FIELD';
                    } else {
                        $field = 'varchar_field';
                    }

                    $value = $newRecord[$field];

                    $this->assertSame(
                        $aeVar,
                        $value,
                        sprintf(
                            '[%s] [FM:%s] updated record should have an varchar_field value %s',
                            $fetchDescription,
                            $qfValue,
                            $aeVar
                        )
                    );
                }
            }
        }
    }
}
