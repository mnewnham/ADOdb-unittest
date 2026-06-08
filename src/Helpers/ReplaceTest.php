<?php

/**
 * Tests for the Replace method
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
class ReplaceTest extends ADOdbTestCase
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
    public function testReplaceMethodInsert(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $absoluteFetchMode = $this->insertFetchMode($fetchMode);

        $aeVar = 'REPLACE01' . $fetchMode . $fetchMode;

        $ar = array(
            'varchar_field' => $aeVar,
            'integer_field' => 99,
            'number_run_field' => 5001 + $fetchMode + (10 * $fetchMode)
        );

        $this->db->startTrans();

        $response = $this->db->replace(
            'autoexecute', 
            $ar, 
            'id',
            false,
            true
        );

        $this->db->completeTrans();

        $this->assertSame(
            2,
            $response,
            sprintf(
                '[FORCEMODE %s][FETCH %s ] Replace() should return 2 ' .
                'If the record is created successfully',
                $fetchMode,
                $fetchDescription
            )
        );
    }

    /**
     * Test for {@see ADODConnection::replace()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:replace
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testReplaceMethodUpdate(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $absoluteFetchMode = $this->insertFetchMode($fetchMode);
        

        $numberRunField = 5001 + $fetchMode + (10 * $fetchMode);

        $SQL = "SELECT id FROM autoexecute WHERE number_run_field=$numberRunField";
        $thisId = $this->db->getOne($SQL);

        $aeVar = 'REPLACE02' . $fetchMode . $fetchMode;

        $ar = array(
            'id' => $thisId,
            'varchar_field' => $aeVar,
            'integer_field' => 99,
            'number_run_field' => 5001 + $fetchMode + (10 * $fetchMode)
        );

        $this->db->startTrans();

        $response = $this->db->replace(
            'autoexecute', 
            $ar, 
            'id',
            false,
            true
        );

        $this->db->completeTrans();

        $this->assertSame(
            1,
            $response,
            sprintf(
                '[FORCEMODE %s][FETCH %s ] Replace() should return 1 ' .
                'If the record is updated successfully',
                $fetchMode,
                $fetchDescription
            )
        );

        $SQL = "SELECT varchar_field FROM autoexecute WHERE id=$thisId";
        $varCharField = $this->db->getOne($SQL);

        $this->assertSame(
            $aeVar,
            $varCharField,
            sprintf(
                '[FORCEMODE %s][FETCH %s ] Replace() should have changed ' .
                'varchar_field to [%s] if the record was updated successfully',
                $fetchMode,
                $fetchDescription,
                $aeVar
            )
        );
    }
}
