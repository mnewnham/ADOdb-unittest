<?php

/**
 * Tests cases for MetaIndexes functions of ADODb
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
 * Class MetaIndexesTest
 *
 * Test cases for for ADOdb MetaIndexes
 */
class MetaIndexesTest extends MetaFunctions
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
     * Test 1 for {@see ADODConnection::metaIndexes()]
     * Checks that the correct number of indexes is returned
     *
     * @return void
     */
    public function testMetaIndexCount(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->insertFetchMode($fetchMode);

            $executionResult = $this->db->metaIndexes(
                $this->testTableName,
                true
            );

            list($errno, $errmsg) = $this->assertADOdbError('metaIndexes()');

            $this->validateResetFetchModes();

            $this->assertIsArray(
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] Checking With Primary MetaIndexes returns an array',
                    $fetchModeName
                )
            );
            $this->assertSame(
                4,
                count($executionResult),
                sprintf(
                    '[FETCH MODE %s] Checking With Primary Index Count should be 4',
                    $fetchModeName
                )
            );

            $executionResult = $this->db->metaIndexes(
                $this->testTableName,
                false
            );

            list($errno, $errmsg) = $this->assertADOdbError('metaIndexes()');

            $this->assertIsArray(
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] Checking No Primary MetaIndexes returns an array',
                    $fetchModeName
                )
            );
            $this->assertSame(
                3,
                count($executionResult),
                sprintf(
                    '[FETCH MODE %s] Checking No Primary Index Count should be 3',
                    $fetchModeName
                )
            );

            $executionResult = array_change_key_case($executionResult, CASE_UPPER);

            /*
            [VDX2] => Array
        (
            [unique] => 1
            [columns] => Array
                (
                    [0] => INTEGER_FIELD
                    [1] => DATE_FIELD
                )

            [primary] => 0
        )
            */
            $this->assertArrayHasKey(
                'VDX2',
                $executionResult,
                'MetaIndexes should return index VDX2'
            );

            $vdx2 = $executionResult['VDX2'];

            $this->assertArrayHasKey(
                'unique',
                $vdx2,
                'MetaIndexes key should have "unique" element'
            );

             $this->assertArrayHasKey(
                 'columns',
                 $vdx2,
                 'MetaIndexes key should have "columns" element'
             );

             $this->assertArrayHasKey(
                 'primary',
                 $vdx2,
                 'MetaIndexes key should have "primary" element'
             );

            $this->assertSame(
                1,
                $vdx2['unique'],
                'MetaIndexes unique key should be "1"'
            );

            $columns = [
                'INTEGER_FIELD',
                'DATE_FIELD'
            ];

             $this->assertSame(
                 $columns,
                 $vdx2['columns'],
                 'MetaIndexes index vdx2 should have 2 columns'
             );

            $this->assertSame(
                0,
                $vdx2['primary'],
                'MetaIndexes primary key should be "0"'
            );

            $this->validateResetFetchModes();
        }
    }

    /**
     * Data provider for {@see testMetaColumns()}
     *
     * @return array [bool array type, array return value]
     */
    public static function providerTestMetaIndex(): array
    {
        $owner = $GLOBALS['DriverControl']->schemaOwner;

        return [
             'Index vdx1 is unique, 1 element, ADODB_FETCH_NUM,' => [
                ADODB_FETCH_NUM, true, 'vdx1', false
                ],
             'Index vdx2 is unique, 2 elements, ADODB_FETCH_NUM' => [
                ADODB_FETCH_NUM, true,'vdx2', false
                ],
             'Index vdx1 is unique, 1 element, ADODB_FETCH_ASSOC,' => [
                ADODB_FETCH_ASSOC, true, 'vdx1', false
                ],
             'Index vdx2 is unique, 2 elements, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC, true,'vdx2', false
                ],
             'Index vdx1 is unique, 1 element, ADODB_FETCH_BOTH,' => [
                ADODB_FETCH_BOTH, true, 'vdx1', false
                ],
             'Index vdx2 is unique, 2 elements, ADODB_FETCH_BOTH' => [
                ADODB_FETCH_BOTH, true,'vdx2', false
                ],
             'Index vdx2 is unique, 2 elements, ADODB_FETCH_BOTH, Owned by me' => [
                ADODB_FETCH_BOTH, true,'vdx2', $owner
                ],
            ];
    }

    /**
     * Test 2 for {@see ADODConnection::metaIndexes()]
     * Checks that the correct unique indexes is returned
     *
     * @param bool $result
     * @param string $indexName
     *
     * @return void
     */
    #[DataProvider('providerTestMetaIndexUniqueness')]
    public function testMetaIndexUniqueness(
        int $fetchMode,
        bool $isUnique,
        string $indexName,
        mixed $owner
    ): void {

        $this->db->setFetchMode($fetchMode);

        $executionResult = $this->db->metaIndexes($this->testTableName, false, $owner);
        list($errno, $errmsg) = $this->assertADOdbError('metaIndexes()');

        $this->assertIsArray(
            $executionResult,
            sprintf(
                '[FETCH MODE %s] Checking MetaIndexes returns an array',
                $this->testFetchModes[$fetchMode]
            )
        );

        /*
        * Don't know the casing on the key so standardize
        */
        $executionResult = array_change_key_case(
            $executionResult,
            CASE_LOWER
        );

        $this->assertSame(
            $isUnique,
            ($executionResult[$indexName]['unique'] == 1),
            sprintf(
                '[FETCH MODE %s] Checking Correctly indicates Index Uniqueness',
                $this->testFetchModes[$fetchMode]
            )
        );
    }

    /**
     * Data provider for {@see testMetaColumns()}
     *
     * @return array [bool array type, array return value]
     */
    public static function providerTestMetaIndexUniqueness(): array
    {
         $owner = $GLOBALS['DriverControl']->schemaOwner;
        return [
             'Index vdx1 is unique, 1 element, ADODB_FETCH_NUM,' => [
                ADODB_FETCH_NUM, true, 'vdx1',false
                ],
             'Index vdx2 is unique, 2 elements, ADODB_FETCH_NUM' => [
                ADODB_FETCH_NUM, true,'vdx2', false
                ],
             'Index vdx1 is unique, 1 element, ADODB_FETCH_ASSOC,' => [
                ADODB_FETCH_ASSOC, true, 'vdx1', false
                ],
             'Index vdx2 is unique, 2 elements, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC, true,'vdx2', false
                ],
             'Index vdx1 is unique, 1 element, ADODB_FETCH_BOTH,' => [
                ADODB_FETCH_BOTH, true, 'vdx1', false
                ],
             'Index vdx2 is unique, 2 elements, ADODB_FETCH_BOTH' => [
                ADODB_FETCH_BOTH, true,'vdx2', false
                ],
             'Index vdx2 is unique, 2 elements, ADODB_FETCH_BOTH, Belongs to me' => [
                ADODB_FETCH_BOTH, true,'vdx2', $owner
                ],
            ];
    }

    /**
     * Test for errors when a meta function is called on an invalid table
     *
     * @return void
     */
    public function testMetaIndexesForInvalidTable(): void
    {


        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->insertFetchMode($fetchMode);

            $response = $this->db->metaIndexes('invalid_table');

            $this->validateResetFetchModes();

            $this->assertFalse(
                $response,
                sprintf(
                    '[FETCH MODE %s] Checking that metaIndexes returns false' .
                    'for an invalid table',
                    $fetchModeName
                )
            );
        }
    }
}
