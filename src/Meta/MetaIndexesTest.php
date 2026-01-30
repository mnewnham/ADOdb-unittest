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
            $this->db->setFetchMode($fetchMode);

            $executionResult = $this->db->metaIndexes($this->testTableName);
            list($errno, $errmsg) = $this->assertADOdbError('metaIndexes()');

            $this->assertSame(
                3,
                count($executionResult),
                sprintf(
                    'FETCH MODE %s] Checking Index Count should be 3',
                    $fetchModeName
                )
            );
        }
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
    public function testMetaIndexUniqueness($result, $indexName): void
    {
        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $executionResult = $this->db->metaIndexes($this->testTableName);
            list($errno, $errmsg) = $this->assertADOdbError('metaIndexes()');

            $this->assertSame(
                $result,
                ($executionResult[$indexName]['unique'] == 1),
                sprintf(
                    '[FETCH MODE %s] Checking Index Uniqueness',
                    $fetchModeName
                )
            );
        };
    }

    /**
     * Data provider for {@see testMetaColumns()}
     *
     * @return array [bool array type, array return value]
     */
    public static function providerTestMetaIndexUniqueness(): array
    {
        return [
             'Index vdx1 is unique' => [true,'vdx1'],
             'Index vdx2 is unique' => [true,'vdx2'],
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
            $this->db->setFetchMode($fetchMode);


            $response = $this->db->metaIndexes('invalid_table');

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
