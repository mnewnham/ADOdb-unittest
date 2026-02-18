<?php

/**
 * Tests cases for MetaColumnNames functions of ADODb
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
 * Class MetaColumnNamesTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class MetaColumnNamesTest extends MetaFunctions
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
     * Test for {@see ADODConnection::metaColumnNames()]
     *
     * @param bool  $numericIndices      Return numeric indexes
     * @param int   $fetchMode           The fetch mode
     * @param array $expectedResult      What should be returned
     * @param bool  $forcePostgresOption Use the PGSQL attnum parameter
     *
     * @return void
     */
    #[DataProvider('providerTestMetaColumnNames')]
    public function testMetaColumnNames(
        bool $numericIndices,
        int $fetchMode,
        array $expectedResult,
        bool $forcePostgresOption
    ): void {

        global $ADODB_FETCH_MODE;

        $this->db->setFetchMode($fetchMode);
        $ADODB_FETCH_MODE = $fetchMode;
        $executionResult = $this->db->metaColumnNames(
            $this->testTableName,
            $numericIndices,
            $forcePostgresOption
        );
        list($errno, $errmsg) = $this->assertADOdbError('metaColumnNames()');

        $executionKeys = array_keys($executionResult);

        if ($forcePostgresOption) {
            $expectedCount = count($expectedResult);
            $executionCount = count($executionKeys);
            $this->assertSame(
                $expectedCount,
                $executionCount,
                'When using the Postgres attnumn option, only the total number of keys is deteeminable'
            );
        } else {
            $this->assertSame(
                $expectedResult,
                $executionKeys,
                sprintf(
                    '[FETCH MODE: %s] Checking Keys of returned data %s',
                    $this->testFetchModes[$fetchMode],
                    $numericIndices ? 'true' : 'false'
                )
            );
        }
    }

    /**
     * Data provider for {@see testMetaColumNames()}
     *
     * @return array [bool array type, array return value]
     */
    public static function providerTestMetaColumnNames(): array
    {
        return [
            'Default Behavior ADODB_FETCH_ASSOC' => [
                false,
                ADODB_FETCH_ASSOC,
                [
                    'ID',
                    'VARCHAR_FIELD',
                    'DATETIME_FIELD',
                    'DATE_FIELD',
                    'INTEGER_FIELD',
                    'DECIMAL_FIELD',
                    'BOOLEAN_FIELD',
                    'EMPTY_FIELD',
                    'NUMBER_RUN_FIELD'
                ],
                false
            ],
            'Default Behavior ADODB_FETCH_NUM' => [
                false,
                ADODB_FETCH_NUM,
                [
                    'ID',
                    'VARCHAR_FIELD',
                    'DATETIME_FIELD',
                    'DATE_FIELD',
                    'INTEGER_FIELD',
                    'DECIMAL_FIELD',
                    'BOOLEAN_FIELD',
                    'EMPTY_FIELD',
                    'NUMBER_RUN_FIELD'
                ],
                false

            ],
            'Force Numeric Array ADODB_FETCH_ASSOC' => [
                true,
                ADODB_FETCH_ASSOC,
                [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ],
                false
            ],
            'Force Postgres Option ADODB_FETCH_ASSOC' => [
                true,
                ADODB_FETCH_ASSOC,
                [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ],
                true
            ]
        ];
    }

    /**
     * Test for errors when a metacolumnNames function is called on an invalid table
     *
     * @return void
     */
    public function testMetaColumnNamesForInvalidTable(): void
    {


        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            //$this->db->setFetchMode($fetchMode);
            $this->insertFetchMode($fetchMode);

            $response = $this->db->metaColumnNames('invalid_table');

            $this->validateResetFetchModes();

            $this->assertFalse(
                $response,
                sprintf(
                    '[FETCH MODE %s] Checking that metaColumnNames returns ' .
                    'false for an invalid table',
                    $fetchModeName
                )
            );
        }
    }
}
