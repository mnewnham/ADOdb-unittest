<?php

/**
 * Tests cases for core SQL functions of ADODb
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
namespace MNewnham\ADOdbUnitTest\CoreModule;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class Sequences
 *
 * Test cases for for ADOdb Sequences Handling
 */
class SequenceTest extends ADOdbTestCase
{
    /**
     * Set up the test environment first time
     *
     * @return void
     */
    public static function setupBeforeClass(): void
    {
        $db        = $GLOBALS['ADOdbConnection'];

        $SQL = "SELECT COUNT(*) AS core_table3_count FROM testtable_3";
        $table3DataExists = $db->getOne($SQL);

        if ($table3DataExists) {
            // Data already exists, no need to reload
            return;
        }

        /*
        *load Data into the table
        */
        $db->startTrans();

        $table3Data = sprintf('%s/../tools/DatabaseSetup/table3-data.sql', dirname(__FILE__));
        $table3Sql = file_get_contents($table3Data);
        $t3Sql = explode(';', $table3Sql);
        foreach ($t3Sql as $sql) {
            if (trim($sql ?? '')) {
                $db->execute($sql);
            }
        }

        $db->completeTrans();
    }


    /**
     * Test for {@see ADODConnection::CreateSequence()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:createsequence
     *
     * @return void
     */
    public function testCreateSequence(): void
    {

        $this->db->startTrans();
        $response = $this->db->CreateSequence('unittest_seq', 50);


        $this->db->completeTrans();

        list($errno, $errmsg) = $this->assertADOdbError('createSequenceSql()');


        $this->assertIsObject(
            $response,
            'CreateSequence should return an object ' .
            'If a sequence is created successfully'
        );


        $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

        $this->assertTrue(
            $ok,
            'CreateSequence should return an ADORecordSet_empty object ' .
            'If a sequence is created successfully'
        );
    }

    /**
     * Tests the genID() method
     *
     * @see https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:genid
     *
     * @return void
     */
    public function testGenID(): void
    {


        $this->db->startTrans();
        $nextId = $this->db->GenID('unittest_seq');

        list($errno,$errmsg) = $this->assertADOdbError('genID()');

        $this->db->completeTrans();

        $this->assertSame(
            50,
            $nextId,
            'GenID should return the initial value of 50 in the sequence'
        );
        $this->assertSame(
            50,
            $nextId,
            'GenID should return the initial value of 50 in the sequence'
        );
    }

    /**
     * Tests the genID() method
     *
     * @see https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:genid
     *
     * @return void
     */
    public function getNextGenID(): void
    {


        $nextId = $this->db->GenID('unittest_seq');

        list($errno, $errmsg) = $this->assertADOdbError('genID()');

        $this->assertSame(
            51,
            $nextId,
            'GenID should return the next value of 51 in the sequence'
        );
    }

    /**
     * Test for {@see ADODConnection::DropSequence()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:dropsequence
     *
     * @return void
     */
    public function testDropSequence(): void
    {
        $this->db->startTrans();
        $response = $this->db->DropSequence('unittest_seq');

        list($errno, $errmsg) = $this->assertADOdbError('dropSequence()');

        $this->db->completeTrans();

        $ok = is_object($response) && get_class($response) == 'ADORecordSet_empty';

        $this->assertTrue(
            $ok,
            'DropSequence should return an ADORecordset_empty ' .
            'object If a sequence is dropped successfully'
        );
    }
}
