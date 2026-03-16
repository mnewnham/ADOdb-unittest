<?php

/**
 * Tests cases for MetaForeignKeys functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\CoreModule;

use MNewnham\ADOdbUnitTest\CoreModule\ADOdbCoreSetup;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaForeignKeysTest
 *
 * Test cases for for ADOdb MetaForeignKeys
 */
class InsertIdTest extends ADOdbCoreSetup
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

        /*
        * load foreign keys test schema
        */
        $db->startTrans();

        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/insert-id-schema.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        /*
        * Loads the schema based on the DB type
        */

        readSqlIntoDatabase($db, $tableSchema);

        $db->completeTrans();
    }

    /**
     * Test successfully incrementing autoincrement columns
     *
     * @return void
     */
    public function testInsertAutoId(): void
    {

        $autoAr = array(
            'integer_field' => 99
        );

        $counter = 1;
        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->startTrans();
            $this->insertFetchMode($fetchMode);

            $this->db->hasInsertID = true;
            $sql = "SELECT * FROM insert_auto WHERE id=-1";
            $autoTemplate = $this->db->execute($sql);

            $sql = $this->db->getInsertSql($autoTemplate, $autoAr);


            $this->db->execute($sql);

            $insertId = $this->db->insert_id();

            $this->assertEquals(
                $counter,
                $insertId,
                sprintf(
                    '[FETCH MODE %s] Auto increment insertid should increment',
                    $fetchModeName
                )
            );


            $this->validateResetFetchModes();

            $this->db->completeTrans();
            $counter++;
        }
    }

    /**
     * Test table with no auto-increment column does not trigger insert_id
     *
     * @return void
     */
    public function testInsertManualId(): void
    {

        $counter = 1;

        $manualAr = [
            'id' => $counter,
            'integer_field' => 99
        ];

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->startTrans();
            $this->insertFetchMode($fetchMode);

            $sql = "SELECT * FROM insert_manual WHERE id=-1";

            $manualTemplate = $this->db->execute($sql);

            $sql = $this->db->getInsertSql($manualTemplate, $manualAr);

            $this->db->execute($sql);

            $insertId = $this->db->insert_id();

            $this->assertEquals(
                0,
                $insertId,
                sprintf(
                    '[FETCH MODE %s] No Auto increment: insertid should never change from 0, currently %d',
                    $fetchModeName,
                    $insertId
                )
            );

            $this->validateResetFetchModes();
            $this->db->completeTrans();
            $manualAr['id']++;
            $counter++;
        }
    }
}
