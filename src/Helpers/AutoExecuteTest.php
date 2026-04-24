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
 * Class AutoExecuteTest
 * Test cases for AutoExecute
 */
class AutoExecuteTest extends ADOdbTestCase
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

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
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


        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
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
     * Test for {@see ADODConnection::getUpdateSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getupdatesql
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testAutoExecuteInsert(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $absoluteFetchMode = $this->insertFetchMode($fetchMode);

        for ($forceMode = 0; $forceMode < 2; $forceMode++) {
            
            $aeVar = 'AUTOEXECUTE01' . $forceMode . $fetchMode;

            $ar = array(
                'varchar_field' => $aeVar,
                'integer_field' => 99,
                'number_run_field' => 5001 + $fetchMode + (10 * $forceMode)
            );

            $this->db->startTrans();

            $response = $this->db->autoExecute('autoexecute', $ar, 'INSERT');

            $this->db->completeTrans();

            if (is_object($response)) {
                $reflection = new \ReflectionClass($response);
                $shortName  = $reflection->getShortName();
                $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

                $this->assertTrue(
                    $ok,
                    sprintf(
                        '[FORCEMODE %s][FETCH %s ] autoExecute should return ' .
                            'an empty ADORecordSet Object If the record is updated successfully',
                        $forceMode,
                        $fetchDescription
                    )
                );
            } else {
                $this->fail(
                    sprintf(
                        '[FORCEMODE %s][FETCH %s ] autoExecute should return ' .
                            'an empty ADORecordSet Object If the record is updated successfully',
                        $forceMode,
                        $fetchDescription
                    )
                );
            }

            $sql = "SELECT varchar_field,integer_field FROM autoexecute ORDER BY id DESC";
            $newRecord = $this->db->getRow($sql);

            if ($fetchMode == 0 || $fetchMode == 3) {
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
                    '[%s] updated record should have an varchar_field value %s in array %s',
                    $fetchDescription,
                    'AUTOEXECUTE01' . $forceMode . $fetchMode,
                    print_r($newRecord, true)
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
    #[DataProvider('globalProviderFetchModes')]
    public function testAutoExecuteUpdate(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $sql = "SELECT id FROM autoexecute ORDER BY id DESC";
        $lastId = $this->db->getOne($sql);

        $where = "id=$lastId";

        for ($forceMode = 0; $forceMode < 2; $forceMode++) {
           
            $aeVar = 'AUTOEXECUTE02' . $forceMode . $fetchMode;

            //$this->db->setFetchMode($fetchMode);
            $this->insertFetchMode($fetchMode);
            $ar = array(
                'varchar_field' => $aeVar,
                'integer_field' => 99,
                'number_run_field' => 7001 + $fetchMode + (10 * ($forceMode + 1))
            );

            $this->db->startTrans();

            $response = $this->db->autoExecute(
                'autoexecute',
                $ar,
                'UPDATE',
                $where,
                $forceMode
            );

            $this->db->completeTrans();

            if (is_object($response)) {
                $reflection = new \ReflectionClass($response);
                $shortName  = $reflection->getShortName();
                $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

                $this->assertTrue(
                    $ok,
                    sprintf(
                        '[FORCEMODE %s][FETCH %s ] autoExecute should return ' .
                            'an empty ADORecordSet Object If the record is updated successfully',
                        $forceMode,
                        $fetchDescription
                    )
                );
            } else {
                $this->fail(
                    sprintf(
                        '[FORCEMODE %s][FETCH %s ] autoExecute should return ' .
                            'an empty ADORecordSet Object If the record is updated successfully',
                        $forceMode,
                        $fetchDescription
                    )
                );
            }

            $sql = "SELECT varchar_field,integer_field FROM autoexecute ORDER BY id DESC";
            $newRecord = $this->db->getRow($sql);

            if ($fetchMode == 0 || $fetchMode == 3) {
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
                    '[%s] updated record should have an varchar_field value %s in array %s',
                    $fetchDescription,
                    'AUTOEXECUTE' . $forceMode . $fetchMode,
                    print_r($newRecord, true)
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
    public function testAutoExecuteUpdateQuoteFieldNames(): void
    {

        global $ADODB_QUOTE_FIELDNAMES;

        $qfArray = [
            true, false, 'BRACKETS', 'UPPER', 'LOWER'
            ];

        foreach ($qfArray as $qfIndex => $qfValue) {
            $ADODB_QUOTE_FIELDNAMES = $qfIndex;

            $sql = "SELECT id FROM autoexecute ORDER BY id DESC";
            $lastId = $this->db->getOne($sql);

            $where = "id=$lastId";

            for ($forceMode = 0; $forceMode < 2; $forceMode++) {
                foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
                    $this->insertFetchMode($fetchMode);

                    $aeVar = 'AUTOEXECUTE03' . $forceMode . $fetchMode . $qfIndex;

                    $nrf = sprintf('8%s01%s%s', $qfIndex, $fetchMode, $forceMode + 1);

                    $ar = array(
                        'varchar_field' => $aeVar,
                        'integer_field' => 99,
                        'number_run_field' => $nrf
                    );

                    $this->db->startTrans();

                    $response = $this->db->autoExecute(
                        'autoexecute',
                        $ar,
                        'UPDATE',
                        $where,
                        $forceMode
                    );

                    $this->db->completeTrans();

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
                    /*
                    $this->assertTrue(
                        $response,
                        sprintf(
                            '[FORCEMODE %s][FETCH %s ] autoExecute should return true ' .
                            'If the record is updated successfully',
                            $forceMode,
                            $fetchDescription
                        )
                    );
                    */

                    if (is_object($response)) {
                        $reflection = new \ReflectionClass($response);
                        $shortName  = $reflection->getShortName();
                        $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

                        $this->assertTrue(
                            $ok,
                            sprintf(
                                '[FORCEMODE %s][FETCH %s ] autoExecute should return ' .
                                 'an empty ADORecordSet Object If the record is updated successfully',
                                $forceMode,
                                $fetchDescription
                            )
                        );
                    } else {
                        $this->fail(
                            sprintf(
                                '[FORCEMODE %s][FETCH %s ] autoExecute should return ' .
                                 'an empty ADORecordSet Object If the record is updated successfully',
                                $forceMode,
                                $fetchDescription
                            )
                        );
                    }


                    $sql = "SELECT varchar_field,integer_field 
                              FROM autoexecute
                          ORDER BY id DESC";


                    $newRecord = $this->db->getRow($sql);

                    if ($fetchMode == 0 || $fetchMode == 3) {
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
