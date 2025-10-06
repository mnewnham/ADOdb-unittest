<?php

/**
 * Tests for the getUpdateSql method
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
 * Class getUpdateSqlTest
 * Test cases for getUpdateSql
 */
class GetUpdateSqlTest extends ADOdbTestCase
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
    public function testGetUpdateSqlWithUnboundAndValidArray(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->db->setFetchMode($fetchMode);

            $sql = "SELECT id FROM {$this->testTableName} ORDER BY id DESC";
            $lastId = $this->db->getOne($sql);

            $sql = "SELECT * FROM {$this->testTableName} WHERE id=$lastId";

            list ($template,$errno,$errmsg) = $this->executeSqlString($sql);

            $ar = array(
                'varchar_field' => 'GETUPDATESQL0' . $fetchMode,
                'integer_field' => 99,
                'number_run_field' => 4001 + $fetchMode
            );

            /*
            * This should create a record populated with default values and the
            * next available id
            */

            $sql = $this->db->getUpdateSql($template, $ar);


            $response = $this->db->execute($sql);


            $this->assertIsObject(
                $response,
                'updates should return an object ' .
                'If the record is created successfully'
            );


            $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

            $this->assertTrue(
                $ok,
                'getUpdateSql should return an ADORecordSet_empty object ' .
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
                'GETUPDATESQL0' . $fetchMode,
                $value,
                sprintf(
                    '[%s] updated record should have an varchar_field value %s',
                    $fetchDescription,
                    'GETUPDATESQL0' . $fetchMode
                )
            );
        }
    }

    /**
     * Test for {@see ADODConnection::getUpdateSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getupdatesql
     *
     * @return void
     */
    public function testGetUpdateSqlWithUnboundAndInvalidArray(): void
    {

        for ($forceMode = 0; $forceMode < 2; $forceMode++) {
            foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
                $this->db->setFetchMode($fetchMode);

                $sql = "SELECT id FROM {$this->testTableName} ORDER BY id DESC";
                $lastId = $this->db->getOne($sql);

                $sql = "SELECT * FROM {$this->testTableName} WHERE id=$lastId";

                list ($template,$errno,$errmsg) = $this->executeSqlString($sql);

                $ar = array(
                    'varchar_field' => 'GETUPDATESQL0' . $fetchMode . $forceMode,
                    'integer_field' => 99,
                    'number_run_field' => 4001 + $fetchMode + (10 * $forceMode),
                    'some_invalid_field' => 'ABC123'
                );

                /*
                * This should create a record populated with default values and the
                * next available id
                */

                $sql = $this->db->getUpdateSql($template, $ar, $forceMode);

                $response = $this->db->execute($sql);

                $this->assertIsObject(
                    $response,
                    'updates should return an object ' .
                    'If the record is created successfully'
                );


                $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

                $this->assertTrue(
                    $ok,
                    'getUpdateSql should return an ADORecordSet_empty object ' .
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
                    'GETUPDATESQL0' . $fetchMode . $forceMode,
                    $value,
                    sprintf(
                        '[%s] [FORCE=%s] updated record should have an varchar_field value %s',
                        $fetchDescription,
                        $forceMode,
                        'GETUPDATESQL0' . $fetchMode  . $forceMode
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
    public function testGetUpdateSqlWithBoundAndValidArray(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->db->setFetchMode($fetchMode);

            $sql = "SELECT id FROM {$this->testTableName} ORDER BY id DESC";
            $lastId = $this->db->getOne($sql);

            $p1 = $this->db->param('p1');
            $bind = array('p1' => $lastId);

            $sql = "SELECT * FROM {$this->testTableName} WHERE id=$p1";

            list ($template,$errno,$errmsg) = $this->executeSqlString($sql, $bind);

            $ar = array(
                'varchar_field' => 'GETUPDATESQL0' . $fetchMode,
                'integer_field' => 99,
                'number_run_field' => 4001 + $fetchMode
            );

            /*
            * This should create a record populated with default values and the
            * next available id
            */

            $sql = $this->db->getUpdateSql($template, $ar);


            $response = $this->db->execute($sql, $bind);


            $this->assertIsObject(
                $response,
                'updates should return an object ' .
                'If the record is created successfully'
            );


            $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

            $this->assertTrue(
                $ok,
                'getUpdateSql should return an ADORecordSet_empty object ' .
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
                'GETUPDATESQL0' . $fetchMode,
                $value,
                sprintf(
                    '[%s] updated record should have an varchar_field value %s',
                    $fetchDescription,
                    'GETUPDATESQL0' . $fetchMode
                )
            );
        }
    }

    /**
     * Test for {@see ADODConnection::getUpdateSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getupdatesql
     *
     * @return void
     */
    public function testGetUpdateSqlWithBoundAndInvalidArray(): void
    {

        for ($forceMode = 0; $forceMode < 2; $forceMode++) {
            foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
                $this->db->setFetchMode($fetchMode);

                $sql = "SELECT id FROM {$this->testTableName} ORDER BY id DESC";
                $lastId = $this->db->getOne($sql);

                $p1 = $this->db->param('p1');
                $bind = array('p1' => $lastId);

                $sql = "SELECT * FROM {$this->testTableName} WHERE id=$p1";

                list ($template,$errno,$errmsg) = $this->executeSqlString($sql, $bind);


                $ar = array(
                    'varchar_field' => 'GETUPDATESQL0' . $fetchMode . $forceMode,
                    'integer_field' => 99,
                    'number_run_field' => 4001 + $fetchMode + (10 * $forceMode),
                    'some_invalid_field' => 'ABC123'
                );

                /*
                * This should create a record populated with default values and the
                * next available id
                */

                $sql = $this->db->getUpdateSql($template, $ar, $forceMode);

                $response = $this->db->execute($sql, $bind);

                $this->assertIsObject(
                    $response,
                    'updates should return an object ' .
                    'If the record is created successfully'
                );


                $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

                $this->assertTrue(
                    $ok,
                    'getUpdateSql should return an ADORecordSet_empty object ' .
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
                    'GETUPDATESQL0' . $fetchMode . $forceMode,
                    $value,
                    sprintf(
                        '[%s] [FORCE=%s] updated record should have an varchar_field value %s',
                        $fetchDescription,
                        $forceMode,
                        'GETUPDATESQL0' . $fetchMode  . $forceMode
                    )
                );
            }
        }
    }
}
