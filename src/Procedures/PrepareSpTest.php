<?php

/**
 * Extended Tests cases for MetaProcedures functions of ADODb
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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaProceduresTest
 *
 * Test cases for for ADOdb MetaProcedures
 */
class PrepareSpTest extends MetaFunctions
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
        return;
        /*
        *load Active record Table and Data into the table
        */
        $db->startTrans();

        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/stored-procedure-test.sql',
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
            //$this->db->setFetchMode($fetchMode);
            $this->insertFetchMode($fetchMode);

            $statement = $this->db->prepareSp('sp_recordset_test');

            $this->validateResetFetchModes();



            /*
            $this->assertIsString(
                $response,
                sprintf(
                    '[FETCH MODE %s] PrepareSp should return an object if successful',
                    $fetchModeName
                )
            );
            */

            $number = 5;
            $success = $this->db->inParameter($statement, $number, 'filter_number');
        }
    }
}
