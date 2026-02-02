<?php

/**
 * Tests cases for MetaColumns functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\Meta;

use MNewnham\ADOdbUnitTest\Meta\MetaFunctions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaColumnsTest
 *
 * Test cases for for ADOdb MetaColumns
 */
class MetaColumnsTest extends MetaFunctions
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
    }

    /**
     * Test 1 for {@see ADODConnection::metaColumns()]
     * Checks that there are right number of columns
     *
     * @return void
     */
    public function testMetaColumnCount(): void
    {
        $expectedResult  = 9;

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);


            $executionResult = $this->db->metaColumns($this->testTableName);
            list($errno, $errmsg) = $this->assertADOdbError('metaColumns()');

            $this->assertIsArray(
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] ' .
                    'Retrieving Metacolumns for table %s should have returned an array to count',
                    $fetchModeName,
                    $this->testTableName
                )
            );

            $this->assertSame(
                $expectedResult,
                count($executionResult),
                sprintf(
                    '[FETCH MODE %s] Checking Column Count, expected %d, got %d',
                    $fetchModeName,
                    $expectedResult,
                    count($executionResult)
                )
            );
        }
    }

    /**
     * Test 3 for {@see ADODConnection::metaColumns()]
     * Checks that every returned element is an ADOFieldObject
     *
     * @return void
     */
    public function testMetaColumnObjects(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $executionResult = $this->db->metaColumns($this->testTableName);
            list($errno, $errmsg) = $this->assertADOdbError('metaColumns()');
            $this->assertIsArray(
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] ' .
                        'Retrieving Metacolumns Objects for table %s should have returned an array',
                    $fetchModeName,
                    $this->testTableName
                )
            );


            foreach ($executionResult as $column => $o) {
                $oType = get_class($o);
                $this->assertSame(
                    'ADOFieldObject',
                    $oType,
                    sprintf(
                        '[FETCH MODE %s] metaColumns should return ' .
                        'an ADOFieldObject object for column %s',
                        $fetchModeName,
                        $column
                    )
                );
            }
        }
    }

    /**
     * Test for {@see ADODConnection::metaColumns()]
     *
     * Checks that the returned columns match the expected ones
     */
    public function testMetaColumns(): void
    {
        $expectedResult = [
            '0' => 'ID',
            '1' => 'VARCHAR_FIELD',
            '2' => 'DATETIME_FIELD',
            '3' => 'DATE_FIELD',
            '4' => 'INTEGER_FIELD',
            '5' => 'DECIMAL_FIELD',
            '6' => 'BOOLEAN_FIELD',
            '7' => 'EMPTY_FIELD'
        ];

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $executionResult = $this->db->metaColumns($this->testTableName);
            list($errno, $errmsg) = $this->assertADOdbError('metaColumns()');

            $this->assertIsArray(
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] ' .
                        'Retrieving Metacolumns for table %s should have returned an array',
                    $fetchModeName,
                    $this->testTableName
                )
            );


            foreach ($expectedResult as $expectedField) {
                $this->assertArrayHasKey(
                    $expectedField,
                    $executionResult,
                    sprintf(
                        '[FETCH MODE %s] ' .
                        'Checking for expected field %s in metaColumns return value, got %s',
                        $fetchModeName,
                        $expectedField,
                        print_r($executionResult, true)
                    )
                );

                if (!isset($executionResult[$expectedField])) {
                    continue;
                }
            }
        }
    }

    /**
     * Test for errors when a metacolumns function is called on an invalid table
     *
     * @return void
     */
    public function testMetaColumnsForInvalidTable(): void
    {


        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);


            $response = $this->db->metaColumns('invalid_table');

            $this->assertFalse(
                $response,
                sprintf(
                    '[FETCH MODE %s] Checking that metaColumns returns ' .
                    'false for an invalid table',
                    $fetchModeName
                )
            );
        }
    }
}
