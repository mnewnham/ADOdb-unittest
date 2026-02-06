<?php

/**
 * Tests cases for MetaTables functions of ADODb
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
 * Class MetaTablesTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class MetaTablesTest extends MetaFunctions
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
     * Test for {@see ADODConnection::metaTables()] Table Section
     *
     * @param bool   $includesTable1
     * @param string $filterType
     * @param string $mask
     *
     * @return void
     */
    #[DataProvider('providerTestMetaTablesForTable')]
    public function testMetaTablesForTable(bool $includesTable1, mixed $filterType, mixed $mask): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $executionResult = $this->db->metaTables(
                $filterType,
                false, //$this->db->database,
                $mask
            );
            list($errno, $errmsg) = $this->assertADOdbError('metaTables()');

            $tableExists = $executionResult && in_array(
                strtoupper($this->testTableName),
                array_map('strtoupper', $executionResult)
            );

            if (is_array($executionResult) && count($executionResult) > 5) {
                $statement = sprintf(
                    '%s and %s others',
                    $executionResult[0],
                    count($executionResult)
                );
            } else {
                $statement = print_r($executionResult, true);
            }

            $this->assertSame(
                $includesTable1,
                $tableExists,
                sprintf(
                    "[FETCH MODE: %s] Table %s should be in metaTables with 
                    filterType %s mask %s, actually returned:
                    %s",
                    $fetchModeName,
                    $this->testTableName,
                    $filterType,
                    $mask,
                    $statement
                )
            );
        }
    }

    /**
     * Data provider for {@see testMetaTables()}
     *
     * @return array [bool match, string $filterType string $mask]
     */
    public static function providerTestMetaTablesForTable(): array
    {
        return [
            'Show both Tables & Views' => [true,false,false],
            'Show only Tables' => [true,'TABLES',false],
            'Show only Views' => [false,'VIEWS',false],
            'Show only [T]ables' => [true,'T',false],
            'Show only [V]iews' => [false,'V',false],
            'Show only tables beginning test%' => [true,false,'test%'],
            'Show only tables beginning notest%' => [false,false,'notest%'],
            'Show only tables matching testtable_1' => [true,'TABLES','testtable_1'],
            'Show both tables and views matching testtable_1%' => [true,false,'testtable_1%'],

           ];
    }

    /**
     * Test for {@see ADODConnection::metaTables()] Table Section
     *
     * @param bool   $includesTable1
     * @param string $filterType
     * @param string $mask
     *
     * @return void
     */
    #[DataProvider('providerTestMetaTablesForView')]
    public function testMetaTablesForView(bool $includesView1, mixed $filterType, mixed $mask): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $executionResult = $this->db->metaTables(
                $filterType,
                false, //$this->db->database,
                $mask
            );
            list($errno, $errmsg) = $this->assertADOdbError('metaTables()');

            $tableExists = $executionResult && in_array(
                strtoupper($GLOBALS['testViewName']),
                array_map('strtoupper', $executionResult)
            );


            if (is_array($executionResult) && count($executionResult) > 5) {
                $statement = sprintf(
                    '%s and %s others',
                    $executionResult[0],
                    count($executionResult)
                );
            } else {
                $statement = print_r($executionResult, true);
            }

            $this->assertSame(
                $includesView1,
                $tableExists,
                sprintf(
                    "[FETCH MODE: %s] View %s should be in metaTables with 
                    filterType %s mask %s, actually returned:
                    %s",
                    $fetchModeName,
                    $GLOBALS['testViewName'],
                    $filterType,
                    $mask,
                    $statement
                )
            );
        }
    }

    /**
     * Data provider for {@see testMetaTables()}
     *
     * @return array [bool match, string $filterType string $mask]
     */
    public static function providerTestMetaTablesForView(): array
    {
        return [
            'Show both Tables & Views' => [true,false,false],
            'Show only Tables' => [false,'TABLES',false],
            'Show only Views' => [true,'VIEWS',false],
            'Show only [T]ables' => [false,'T',false],
            'Show only [V]iews' => [true,'V',false],
            'Show only views beginning test%' => [true,false,'test%'],
            'Show only views beginning notest%' => [false,false,'notest%'],
            'Show only views matching testtable_1_view' => [true,'VIEWS','testtable_1_view']
        ];
    }

    /**
     * Test for {@see ADODConnection::metaTables()]
     *
     * Checks that an exact table name that exists in the database
     * returns an array with exactly one element
     * that element being the exact table name
     *
     * @return void
     */
    public function testExactMatchMetaTables(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $executionResult = $this->db->metaTables(
                'T',
                false, //$this->db->database,
                $this->testTableName,
            );
            list($errno, $errmsg) = $this->assertADOdbError('metaTables()');

            $assertionResult = $this->assertIsArray(
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] metaTables returns an array when exact ' .
                    'table name that exists in the database is provided',
                    $fetchModeName
                )
            );

            if ($assertionResult) {
                $assertionResult = $this->assertEquals(
                    1,
                    count($executionResult),
                    sprintf(
                        '[FETCH MODE %s] metaTables should return an array ' .
                        'with exactly one element when exact table name ' .
                        'that exists in the database is provided',
                        $fetchModeName
                    )
                );
                if ($assertionResult) {
                    $this->assertSame(
                        strtoupper($this->testTableName),
                        strtoupper($executionResult[0]),
                        sprintf(
                            '[FETCH MODE %s] metaTables should return an array ' .
                            'with the exact table name when exact table name ' .
                            'that exists in the database is provided',
                            $fetchModeName
                        )
                    );
                }
            }
        }
    }
}
