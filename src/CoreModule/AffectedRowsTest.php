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
 * Test cases for for ADOdb affected_row()s
 */
class AffectedRowsTest extends ADOdbCoreSetup
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
    public function testUpdateAffectedRowsWithoutBind(): void
    {

        $this->db->startTrans();

        $SQL = "UPDATE insert_auto 
                   SET integer_field=50
                 WHERE id<51";
        $this->db->execute($SQL);

        $this->assertEquals(
            50,
            $this->db->affected_rows(),
            'Affected_rows shoud return 100 from update'
        );

        $this->db->completeTrans();
    }

     /**
     * Test count against bound update
     *
     * @return void
     */
    public function testUpdateAffectedRowsWithBind(): void
    {


        $this->db->startTrans();

        $p1 = $this->db->param('p1');

        $bind = ['p1' => 50];

        $SQL = "UPDATE insert_auto 
                   SET integer_field=50
                 WHERE id>$p1";
        $this->db->execute($SQL, $bind);

        $this->assertEquals(
            50,
            $this->db->affected_rows(),
            'Affected_rows shoud return 100 from update'
        );

        $this->db->completeTrans();
    }

    /**
     * Test affected_rows against a select statement
     *
     * @return void
     */
    public function testSelectAffectedRowsValue(): void
    {


        $this->db->startTrans();

        $SQL = "SELECT * 
                  FROM insert_auto 
                 WHERE id<51";
        $this->db->execute($SQL);

        $this->assertEquals(
            0,
            $this->db->affected_rows(),
            'Affected_rows shoud return 0 for a select statement'
        );

        $this->db->completeTrans();
    }

    /**
     * Test successfully deleting rows
     *
     * @return void
     */
    public function testDeleteAffectedRows(): void
    {


        $this->db->startTrans();

        $SQL = "DELETE FROM insert_auto";
        $this->db->execute($SQL);

        $this->assertEquals(
            100,
            $this->db->affected_rows(),
            'Affected_rows shoud return 100 from deletion'
        );

        $this->db->completeTrans();
    }

    /**
     * Test deletion count against an empty table
     *
     * @return void
     */
    public function testDeleteAffectedRowsAgain(): void
    {

        $this->db->startTrans();

        $SQL = "DELETE FROM insert_auto";
        $this->db->execute($SQL);

        $this->assertEquals(
            0,
            $this->db->affected_rows(),
            'Affected_rows shoud return 0 from deletion of empty table'
        );

        $this->db->completeTrans();
    }
}
