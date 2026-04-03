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
 * @copyright 2025,2026 Mark Newnham
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
    #[DataProvider('globalProviderFetchModes')]
    public function testMetaColumnCount(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $expectedResult  = 9;

        $this->insertFetchMode($fetchMode);

        $executionResult = $this->db->metaColumns($this->testTableName);
        list($errno, $errmsg) = $this->assertADOdbError('metaColumns()');

        $this->validateResetFetchModes();

        $this->assertIsArray(
            $executionResult,
            sprintf(
                '[FETCH MODE %s] ' .
                'Retrieving Metacolumns for table %s should have returned an array to count',
                $fetchDescription,
                $this->testTableName
            )
        );

        $this->assertSame(
            $expectedResult,
            count($executionResult),
            sprintf(
                '[FETCH MODE %s] Checking Column Count, expected %d, got %d',
                $fetchDescription,
                $expectedResult,
                count($executionResult)
            )
        );
    }

    /**
     * Test 3 for {@see ADODConnection::metaColumns()]
     * Checks that every returned element is an ADOFieldObject
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testMetaColumnObjects(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $this->insertFetchMode($fetchMode);

        $executionResult = $this->db->metaColumns($this->testTableName);
        list($errno, $errmsg) = $this->assertADOdbError('metaColumns()');

        $this->validateResetFetchModes();

        $this->assertIsArray(
            $executionResult,
            sprintf(
                '[FETCH MODE %s] ' .
                    'Retrieving Metacolumns Objects for table %s should have returned an array',
                $fetchDescription,
                $this->testTableName
            )
        );


        foreach ($executionResult as $column => $o) {
            $reflection = new \ReflectionClass($o);
            $oType = $reflection->getShortName();

            $this->assertSame(
                'ADOFieldObject',
                $oType,
                sprintf(
                    '[FETCH MODE %s] metaColumns should return ' .
                    'an ADOFieldObject object for column %s',
                    $fetchDescription,
                    $column
                )
            );
        }
    }

    /**
     * Test for {@see ADODConnection::metaColumns()]
     *
     * Checks that the returned columns match the expected ones
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testMetaColumns(
        int $fetchMode,
        string $fetchDescription
    ): void {

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

        $absoluteFetchMode = $this->insertFetchMode($fetchMode);

        $executionResult = $this->db->metaColumns($this->testTableName);
        list($errno, $errmsg) = $this->assertADOdbError('metaColumns()');

        $this->validateResetFetchModes();

        $this->assertIsArray(
            $executionResult,
            sprintf(
                '[FETCH MODE %s] ' .
                    'Retrieving Metacolumns for table %s should have returned an array',
                $fetchDescription,
                $this->testTableName
            )
        );

        if ($absoluteFetchMode == ADODB_FETCH_NUM) {
            foreach ($expectedResult as $expectedKey => $expectedField) {
                $this->assertArrayHasKey(
                    $expectedKey,
                    $executionResult,
                    sprintf(
                        '[FETCH MODE %s] ' .
                        'Checking for expected key %s in metaColumns return value, got %s',
                        $fetchDescription,
                        $expectedField,
                        print_r($executionResult, true)
                    )
                );
            }
        } else {
            foreach ($expectedResult as $expectedField) {
                $this->assertArrayHasKey(
                    $expectedField,
                    $executionResult,
                    sprintf(
                        '[FETCH MODE %s] ' .
                        'Checking for expected field %s in metaColumns return value, got %s',
                        $fetchDescription,
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
    #[DataProvider('globalProviderFetchModes')]
    public function testMetaColumnsForInvalidTable(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $this->insertFetchMode($fetchMode);

        $response = $this->db->metaColumns('invalid_table');

        $this->validateResetFetchModes();

        $this->assertFalse(
            $response,
            sprintf(
                '[FETCH MODE %s] Checking that metaColumns returns ' .
                'false for an invalid table',
                $fetchDescription
            )
        );
    }
}
