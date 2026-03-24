<?php

/**
 * Test for a Stored Procedure that returns a result set
 *
 * The tests mst be explicitly enabled to run
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

namespace MNewnham\ADOdbUnitTest\Procedures;

use MNewnham\ADOdbUnitTest\Meta\MetaFunctions;

/**
 * Class ResultSetSpTest
 *
 * Test cases for for ADOdb Stored Procedures
 */
class ResultSetSpTest extends MetaFunctions
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();

        $db        = $GLOBALS['ADOdbConnection'];
        $adoDriver = $GLOBALS['ADOdriver'];

        if ($GLOBALS['skipStoredProcedureTests'] == '1') {
            return;
        }
       
        /*
        * Load Result Set Stored Procedure into DB
        */
        $db->startTrans();

        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/stored-procedure-recordset-test.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        readSqlIntoDatabase($db, $tableSchema);

        $db->completeTrans();
    }

    /**
     * Tests the ADOdb Metaprocedures Function
     *
     * @todo Make this actually test something
     *
     * @return void
     */
    public function testPrepareSp(): void
    {

        if ($GLOBALS['skipStoredProcedureTests'] == '1') {
            $this->markTestSkipped(
                'Stored procedure tests must be explicitly activated'
            );
            return;
        }

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
   
            $absoluteFetchMode = $this->insertFetchMode($fetchMode);

            $statement = $this->db->prepareSp('sp_recordset_test');

             $this->assertIsArray(
                $statement,
                sprintf(
                    '[FETCH %s] prepareSp should return an array of attachment information',
                    $fetchModeName
                )
            );

            $this->assertIsString(
                $statement[0],
                sprintf(
                    '[FETCH %s] prepareSp[0] should return a calling method',
                    $fetchModeName
                )
            );

            $this->assertIsResource(
                $statement[1],
                sprintf(
                    '[FETCH %s] prepareSp[1] should return a resource to attach to',
                    $fetchModeName
                )
            );

            $this->validateResetFetchModes();

            $parameterValue = 5;
            $parameterName = 'filter_number';
            $success = $this->db->inParameter($statement, $parameterValue, $parameterName);

            $this->assertTrue(
                $success,
                sprintf(
                    '[FETCH %s] Parameter "filter_number" should have bound to statement using inParameter()',
                    $fetchModeName
                )
            );

            $result = $this->db->execute($statement);

            $this->assertIsObject(
                $result,
                'Execution of Stored Procedure should return an ADORecordSet object'
            );

            $rowCount    = 0;
            $columnData  = [];
            while ($r = $result->fetchRow()) {
                $rowCount++;
                $columnData = $r;
            }

            $this->assertEquals(
                4,
                $rowCount,
                sprintf(
                    '[FETCH %s] Execution of Stored Procedure should return 4 rows',
                    $fetchModeName
                )
            );

            if ($absoluteFetchMode == ADODB_FETCH_BOTH) {
                $columnCount = 12;
            } else {
                $columnCount = 6;
            }

            $this->assertEquals(
                $columnCount,
                count($columnData),
                sprintf(
                    '[FETCH %s] Each fetched row should contain %s columns',
                    $fetchModeName,
                    $columnCount
                )
            );
        }
    }
}
