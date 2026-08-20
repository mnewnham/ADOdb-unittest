<?php

/**
 * Tests cases for Force Insert functions of ADODb
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

namespace MNewnham\ADOdbUnitTest;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class ForceInsertTest
 *
 * Test cases for for ADOdb Force mode settings. 
 * 
 * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_force_type
 */
class ForceInsertTest extends ADOdbTestCase
{
    protected array $forceModeDescriptions = [
        'ADODB_FORCE_IGNORE',
        'ADODB_FORCE_NULL',
        'ADODB_FORCE_EMPTY',
        'ADODB_FORCE_VALUE',
        'ADODB_FORCE_NULL_AND_ZERO'
    ];
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

     //$GLOBALS['ADOdbConnection']->debug = true;
        if ($GLOBALS['DriverControl']->supportsDropIfExists) {
       
            if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
                $GLOBALS['ADOdbConnection']->startTrans();
            }

            $GLOBALS['ADOdbConnection']->execute("DROP TABLE IF EXISTS force_insert_test");

            if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
                $GLOBALS['ADOdbConnection']->completeTrans();
            }
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
     * Test Forcing the table creation without DB defaults
     *
     * @return void
     */
    public function testForceTablesCreation(): void
    {
        /*
        * Load the table to test insert defaults
        */
        $schemaFile = sprintf(
            '%s/DatabaseSetup/%s/force-insert-test.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );


        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $this->db->startTrans();
        }

        $ok = readSqlIntoDatabase($GLOBALS['ADOdbConnection'], $schemaFile);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $this->db->completeTrans();
        }

        $this->assertIsObject(
            $ok,
            'Force Schema Creation File parsing failed'
        );

        if (!$ok) {
            $this->markTestSkipped('Force Schema Creation parsing failed');
            $this->skipFollowingTests = true;
            return;
        }
    }

     /**
     * Test for {@see ADODConnection::force insert()] Table Section
     *
     * @param integer $forceMode        The ADODB_FORCE_MODE value
     * @param array   $columnValueArray The values to insert
     * 
     * @return void
     */
    #[DataProvider('providerTestDefaultColumns')]
    public function testDefaultColumns(
        int $forceMode, 
        array $columnValueArray
        ): void {

        $columnValues = array_values($columnValueArray);
        static $template = false;

        global $ADODB_FORCE_TYPE;

        $ADODB_FORCE_TYPE = $forceMode;

        $this->db->startTrans();
        $this->db->execute('DELETE FROM adodb_force_insert');
        $this->db->completeTrans();

        $sql = "SELECT * FROM adodb_force_insert WHERE id=-1";
        $template = $this->db->execute($sql);

        /*
            varchar_field VARCHAR(20),
            datetime_field DATETIME,
            date_field DATE,
            integer_field INT(4),
            decimal_field DECIMAL(12.2),
            boolean_field BOOLEAN,
            trigger_field TINYINT,
            */
        $ar = [

            'varchar_field' => 'SOME VALUE',
            'another_varchar_field' => '',
            'datetime_field' => '',
            'date_field' => '',
            'integer_field' => '',
            'decimal_field' => '',
            'boolean_field' => '',
            'trigger_field' => 9
        ];
       

        $vak = array_keys($ar);

        $xar = [
            'varchar_field' => 'SOME VALUE'
        ];

        $tTable = 'adodb_force_insert';
        $sql = $this->db->getInsertSql($template, $ar, false);

        $this->db->startTrans();
        $response = $this->db->execute($sql);
        $this->db->completeTrans();

        $this->assertIsObject(
            $response,
            'insertion should return an object ' .
            'If the record is created successfully'
        );


        $this->db->setFetchMode(ADODB_FETCH_NUM);

        $sql = "SELECT * FROM adodb_force_insert";

        $insertResult = $this->db->getRow($sql);

        //print_r($insertResult);
        //print_r($columnValues);

        foreach ($insertResult as $index => $value) {
            if ($index < 2) {
                continue;
            }
            if ($index == 7) {
                break;
            }

            $expected = 'UNKNOWN';
            $actual    = 'UNKNOWN';

            if (is_null($value)) {
                $actual = 'NULL';
            } elseif ($value === 0) {
                $actual = 'ZERO';
            } elseif ($value == null) {
                $actual = 'NULL';
            } elseif ($value == '') {
                $actual = 'BLANK';
            } elseif ((int)$value == 0) {
                $actual = 'ZERO';
            }


            if (is_null($columnValues[$index])) {
                $expected = 'NULL';
            } elseif (strlen($columnValues[$index]) == 1) {
                $expected = 'ZERO';
            } elseif ($columnValues[$index] == 'ZERO') {
                $expected = 'ZERO';
            } elseif ($columnValues[$index] == 'BLANK') {
                $expected = 'BLANK';
            } elseif ($columnValues[$index] == '') {
                $expected = 'NULL';
            } elseif ($columnValues[$index] == 'NULL') {
                $expected = 'NULL';
            }

           //  print "FM=$forceMode {$this->forceModeDescriptions[$forceMode]} INDEX=$index V=$value CV={$columnValues[$index]} E=$expected A=$actual\n";

            $this->assertSame(
                $expected,
                $actual,
                sprintf(
                    'Force Mode [%s]: Index [%s] %s is %s, should be %s',
                    $this->forceModeDescriptions[$forceMode],
                    $index,
                    $vak[$index],
                    $actual,
                    $expected
                )
            );
        }
    }

    /**
     * Data provider for {@see testMetaTables()}
     *
     * @return array [int $forceMode, array $columnResults]
     */
    public static function providerTestDefaultColumns(): array
    {
        /*
        * 0 = ignore empty fields. All empty fields in array are ignored.
        * 1 = force null. All empty, php null and string 'null' fields are
        *     changed to sql NULL values.
        * 2 = force empty. All empty, php null and string 'null' fields are
        *     changed to sql empty '' or 0 values.
        * 3 = force value. Value is left as it is. Php null and string 'null'
        *     are set to sql NULL values and empty fields '' are set to empty '' sql values.
        * 4 = force value. Like 1 but numeric empty fields are set to zero.
        */

        return [

            'ADODB_FORCE_IGNORE' => [
                ADODB_FORCE_IGNORE,
                [1, 'SOME VALUE', null, null, null, null, null, null]
            ],
            'ADODB_FORCE_NULL' => [
                ADODB_FORCE_NULL,
                 //[2, 'SOME VALUE', null, null, null, null, null, null]
                [
                    'id' => 2,
                    'varchar_field' => 'SOME VALUE',
                    'another_varchar_field' => 'NULL',
                    'datetime_field' => 'NULL',
                    'date_field' => 'NULL',
                    'integer_field' => 'NULL',
                    'decimal_field' => 'NULL',
                    'boolean_field' => 'NULL',
                    'trigger_field' =>  0,
                ]
            ],
            'ADODB_FORCE_EMPTY' => [
                ADODB_FORCE_EMPTY,
                //[3, 'SOME VALUE', 'BLANK', null, null,0, 0, 0, 0, 0]
                [
                    'id' => 3,
                    'varchar_field' => 'SOME VALUE',
                    'another_varchar_field' => 'BLANK',
                    'datetime_field' => 'NULL',
                    'date_field' => 'NULL',
                    'integer_field' => 'ZERO',
                    'decimal_field' => 'ZERO',
                    'boolean_field' => 'ZERO',
                    'trigger_field' =>  0,
                ]
            ],
            'ADODB_FORCE_VALUE' => [
                ADODB_FORCE_VALUE, 
                //[4, 'SOME_VALUE', null, null,null, null, 0, 0, 0, 0, 0]
                [
                    'id' => 4,
                    'varchar_field' => 'SOME VALUE',
                    'another_varchar_field' => 'BLANK',
                    'datetime_field' => 'NULL',
                    'date_field' => 'NULL',
                    'integer_field' => 'ZERO',
                    'decimal_field' => 'ZERO',
                    'boolean_field' => 'ZERO',
                    'trigger_field' =>  0,
                ]
            ],
            'ADODB_FORCE_NULL_AND_ZERO' => [
                ADODB_FORCE_NULL_AND_ZERO,
                [
                    'id' => 5,
                    'varchar_field' => 'SOME VALUE',
                    'another_varchar_field' => 'NULL',
                    'datetime_field' => 'NULL',
                    'date_field' => 'NULL',
                    'integer_field' => 'ZERO',
                    'decimal_field' => 'ZERO',
                    'boolean_field' => 'ZERO',
                    'trigger_field' =>  0,
                ]
            ]
        ];
    }
}
