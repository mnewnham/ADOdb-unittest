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
     * @param bool  $returnType
     * @param array $expectedResult
     *
     * @return void
     */
    #[DataProvider('providerTestMetaColumnNames')]
    public function testMetaColumnNames(bool $returnType, int $fetchMode, array $expectedResult): void
    {
        $this->db->setFetchMode($fetchMode);

        $executionResult = $this->db->metaColumnNames(
            $this->testTableName,
            $returnType
        );
        list($errno, $errmsg) = $this->assertADOdbError('metaColumnNames()');

        $this->assertSame(
            $expectedResult,
            $executionResult,
            sprintf(
                '[FETCH MODE: %s] Checking metaColumnNames with returnType %s',
                $this->testFetchModes[$fetchMode],
                $returnType ? 'true' : 'false'
            )
        );
    }

    /**
     * Data provider for {@see testMetaColumNames()}
     *
     * @return array [bool array type, array return value]
     */
    public static function providerTestMetaColumnNames(): array
    {
        return array(
            'Returning Associative Array' => array(
                false,
                ADODB_FETCH_ASSOC,
                array (
                    'ID' => 'id',
                    'VARCHAR_FIELD' => 'varchar_field',
                    'DATETIME_FIELD' => 'datetime_field',
                    'DATE_FIELD' => 'date_field',
                    'INTEGER_FIELD' => 'integer_field',
                    'DECIMAL_FIELD' => 'decimal_field',
                    'BOOLEAN_FIELD' => 'boolean_field',
                    'EMPTY_FIELD' => 'empty_field',
                    'NUMBER_RUN_FIELD' => 'number_run_field'
                )
            ),

            'Returning Numeric Array' => array(
                true,
                ADODB_FETCH_NUM,
                array(
                    '0' => 'id',
                    '1' => 'varchar_field',
                    '2' => 'datetime_field',
                    '3' => 'date_field',
                    '4' => 'integer_field',
                    '5' => 'decimal_field',
                    '6' => 'boolean_field',
                    '7' => 'empty_field',
                    '8' => 'number_run_field'
                )
            )
        );
    }

    /**
     * Test for errors when a metacolumnNames function is called on an invalid table
     *
     * @return void
     */
    public function testMetaColumnNamesForInvalidTable(): void
    {


        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);


            $response = $this->db->metaColumnNames('invalid_table');

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
