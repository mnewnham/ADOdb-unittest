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
 * @copyright 2025,2026 Mark Newnham
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
        *load Data into the table, checking for driver specific loader
        */
        $db->startTrans();
        
        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/table3-data.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        if (!file_exists($tableSchema)) {

            $tableSchema = sprintf(
                '%s/DatabaseSetup/table3-data.sql',
                $GLOBALS['unitTestToolsDirectory']
            );
        }

        /*
        * Loads the schema based on the DB type
        */
        readSqlIntoDatabase($db, $tableSchema);
        
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

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions){
            $this->db->startTrans();
        }
        
        $response = $this->db->CreateSequence('unittest_seq', 50);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions){
            $this->db->completeTrans();
        }

        list($errno, $errmsg) = $this->assertADOdbError('createSequenceSql()');


        $this->assertIsObject(
            $response,
            'CreateSequence should return an object ' .
            'If a sequence is created successfully'
        );


        if (is_object($response)) {
            $reflection = new \ReflectionClass($response);
            $shortName  = $reflection->getShortName();
            $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

            $this->assertTrue(
                $ok,
                'ADOConnection::createSequence ' .
                'should return empty ADORecordSet, returned ' . $shortName
            );
        }
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
        //$this->db->startTrans();
        $response = $this->db->DropSequence('unittest_seq');

        list($errno, $errmsg) = $this->assertADOdbError('dropSequence()');

        //$this->db->completeTrans();
        if (is_object($response)) {
            $reflection = new \ReflectionClass($response);
            $shortName  = $reflection->getShortName();
            $ok = in_array($shortName, ['ADORecordSet_empty', 'ADORecordSetEmpty']);

            $this->assertTrue(
                $ok,
                'ADOConnection::dropSequence ' .
                'should return empty ADORecordSet on success, returned ' . $shortName
            );
        }
    }
}
