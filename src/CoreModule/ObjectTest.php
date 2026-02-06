<?php

/**
 * Tests cases for recordset as object handling of ADODb
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
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class ObjectTest extends ADOdbTestCase
{
    protected ?array $comparison       = null;
    protected ?array $comparisonLcKeys = null;
    protected ?array $comparisonUcKeys = null;

    protected string $setupSql = "SELECT * FROM testtable_3 ORDER BY id";

    protected ?object $fetchRecordSet = null;

    protected string $idKey = 'id';



    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();

        if (is_array($this->comparison)) {
            return;
        }

         $this->db->setFetchMode(ADODB_FETCH_ASSOC);

        $this->comparison = $this->db->getAll($this->setupSql);

        $this->comparisonLcKeys = array_map(
            'strtolower',
            array_keys($this->comparison[0])
        );

        $this->comparisonUcKeys = array_map(
            'strtoupper',
            array_keys($this->comparison[0])
        );

        if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            $this->idKey = 'ID';
        }

        list(
            $this->fetchRecordSet,
            $errno,
            $errmsg
        ) = $this->executeSqlString($this->setupSql);
    }

    /**
     * Set up the test environment first time
     *
     * @return void
     */
    public static function setupBeforeClass(): void
    {

        $db        = $GLOBALS['ADOdbConnection'];
        $adoDriver = $GLOBALS['ADOdriver'];

        $SQL = "SELECT COUNT(*) AS core_table3_count FROM testtable_3";
        $table3DataExists = $db->getOne($SQL);

        if ($table3DataExists) {
            // Data already exists, no need to reload
            /*
            * Fixes previously damaged transactions if necessary
            */
            $db->startTrans();
            $SQL = "UPDATE testtable_3 SET varchar_field='LINE 1' WHERE varchar_field IS NULL";
            $db->execute($SQL);
            $db->completeTrans();
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
     * Test for {@see ADODConnection::execute() for fetching objects]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     */
    public function testFetchFirstObj(): void
    {

        /*
        * Must put the record pointer in place first
        */
        $this->fetchRecordSet->move(0);

        $object = $this->fetchRecordSet->fetchObj();

        $this->assertIsObject(
            $object,
            'FetchObj() should return an Object containg the first records data'
        );

        foreach ($this->comparisonLcKeys as $key) {
            /*
            * Cannot find empty_field because of the way
            * that the fetchObj object is defined
            */
            if ($key == 'empty_field') {
                continue;
            }
            if (!isset($object->$key)) {
                $this->assertSame(
                    true,
                    false,
                    'fetchObj object should have a lowercase property ' .
                    $key .  ' that match record keys'
                );
            }
        }
    }

    /**
     * Test for {@see ADODConnection::execute() for fetching objects]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     */
    public function testRefetchFirstObj(): void
    {

        $nextObject = $this->fetchRecordSet->fetchObj();

        $this->assertEquals(
            $this->comparison[0][$this->idKey],
            $nextObject->id,
            'fetchObj() should not advance the record pointer'
        );
    }

    /**
     * Test for {@see ADODConnection::execute() for fetching next objects]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     */
    public function testFetchNextObj(): void
    {
        $nextObject = $this->fetchRecordSet->fetchNextObj();

        $this->assertEquals(
            $this->comparison[0][$this->idKey],
            $nextObject->id,
            'fetchNextObj() should not advance the record pointer ' .
            'until after this record is read'
        );


        $nextObject = $this->fetchRecordSet->fetchNextObj();

        $this->assertEquals(
            $this->comparison[1][$this->idKey],
            $nextObject->id,
            'fetchNextObj() should have advanced the record pointer ' .
            'before this record was read'
        );
    }

    /**
     * Test for {@see ADODConnection::execute() for fetching next objects]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     */
    public function testFetchObjToEndOfSet()
    {

        while (!$this->fetchRecordSet->EOF) {
            $nextObject = $this->fetchRecordSet->fetchNextObj();
        }

        $this->assertIsObject(
            $nextObject,
            'fetchNextObj() should leave last record in the buffer after ' .
            'advancing the record pointer beyond EOF'
        );
    }

    /**
     * Test for {@see ADODConnection::execute() for fetching objects]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     */
    public function testFetchFirstObject(): void
    {

         /*
        * Must put the record pointer in place first
        */
        $this->fetchRecordSet->move(0);

        $object = $this->fetchRecordSet->fetchObject();

        $this->assertIsObject(
            $object,
            'FetchObject() should return an Object containg the first records data'
        );

        foreach ($this->comparisonUcKeys as $key) {
            /*
            * Cannot find empty_field because of the way
            * that the fetchObj object is defined
            */
            if ($key == 'EMPTY_FIELD') {
                continue;
            }
            if (!isset($object->$key)) {
                $this->assertSame(
                    true,
                    false,
                    'fetchObject object should have a uppercase property ' .
                    $key .  ' that match record keys'
                );
            }
        }
    }

    /**
     * Test for {@see ADODConnection::execute() for fetching objects]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     */
    public function testRefetchFirstObject(): void
    {

        $nextObject = $this->fetchRecordSet->fetchObject();

        $this->assertEquals(
            $this->comparison[0][$this->idKey],
            $nextObject->ID,
            'fetchObj() should not advance the record pointer'
        );
    }

    /**
     * Test for {@see ADODConnection::execute() for fetching next objects]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     */
    public function testFetchNextObject(): void
    {
        $nextObject = $this->fetchRecordSet->fetchNextObject();

        $this->assertEquals(
            $this->comparison[0][$this->idKey],
            $nextObject->ID,
            'fetchNextObject() should not advance the record pointer ' .
            'until after this record is read'
        );


        $nextObject = $this->fetchRecordSet->fetchNextObject();

        $this->assertEquals(
            $this->comparison[1][$this->idKey],
            $nextObject->ID,
            'fetchNextObject() should have advanced the record pointer ' .
            'before this record was read'
        );
    }

    /**
     * Test for {@see ADODConnection::execute() for fetching next objects]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     */
    public function testFetchObjectToEndOfSet()
    {

        while (!$this->fetchRecordSet->EOF) {
            $nextObject = $this->fetchRecordSet->fetchNextObject();
        }

        $this->assertIsObject(
            $nextObject,
            'fetchNextObject() should leave last record in the buffer after ' .
            'advancing the record pointer beyond EOF'
        );
    }
}
