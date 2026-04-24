<?php

/**
 * Tests cases for Affected Rows of ADODb
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
 * Class AffectedRowsCount
 *
 * Test cases for for ADOdb recordCount
 */
class RecordCountTest extends ADOdbCoreSetup
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        $db        = $GLOBALS['ADOdbConnection'];

        /*
        * load simple insertion schema
        */


        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/insert-id-schema.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        /*
        * Loads the schema based on the DB type
        */
        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->startTrans();
        }

        readSqlIntoDatabase($db, $tableSchema);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->completeTrans();
        }

    }

    public function setup(): void
    {
        parent::setup();
    
        $db = $this->db;

        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/insert-id-schema.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        /*
        * Loads the schema based on the DB type
        */
        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->startTrans();
        }

        readSqlIntoDatabase($db, $tableSchema);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->completeTrans();
        }

        $db->startTrans();

        $sql = "SELECT * FROM insert_auto WHERE id=-1";
        $autoTemplate = $db->execute($sql);

        $autoAr = array(
            'integer_field' => 99
        );

        for ($i = 1; $i <= 100; $i++) {
            $sql = $db->getInsertSql($autoTemplate, $autoAr);
            $db->execute($sql);
        }

        $db->completeTrans();
    }

    /**
     * Test update against an unbound statement
     *
     * @return void
     */
    public function testRecordCountWithoutBind(): void
    {

        $this->db->startTrans();

        $SQL = "SELECT * FROM insert_auto 
                 WHERE id<51";
        $result = $this->db->execute($SQL);

        $this->assertEquals(
            50,
            $result->recordCount(),
            'RecordCount shoud return 50 from base SELECT'
        );

        $this->db->completeTrans();
    }

     /**
     * Test count against bound update
     *
     * @return void
     */
    public function testRecordCountWithBind(): void
    {


        $this->db->startTrans();

        $p1 = $this->db->param('p1');

        $bind = ['p1' => 50];

        $SQL = "SELECT * FROM insert_auto 
                 WHERE id>$p1";
        $result = $this->db->selectLimit($SQL, 10, -1, $bind);

        $this->assertEquals(
            10,
            $result->recordCount(),
            'RecordCount shoud return 50 from SELECTLIMIT'
        );

        $this->db->completeTrans();
    }

    /**
     * Test affected_rows against a select statement
     *
     * @return void
     */
    public function testSelectWithPoRecordCount(): void
    {


        $SQL = "SELECT * 
                  FROM insert_auto
                  WHERE id<51";
        $result = $this->db->execute($SQL);

        $this->assertEquals(
            50,
            $result->po_recordcount(),
            'po_recordcount should return 100 rows for a select statement'
        );

        $this->db->completeTrans();
    }
}
