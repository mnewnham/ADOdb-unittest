<?php

/**
 * Tests cases for MetaPrimaryKeys functions of ADODb
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
 * Class MetaPrimaryKeysTest
 *
 * Test cases for for ADOdb MetaPrimaryKeys
 */
class MetaPrimaryKeysTest extends MetaFunctions
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
     * Test for {@see ADODConnection::metaPrimaryKeys()]
     *
     * Checks that the correct primary key is returned
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:metaprimarykeys
     *
     * @return void
     */
    public function testMetaPrimaryKeys(): void
    {
        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $executionResult = $this->db->metaPrimaryKeys($this->testTableName);
            list($errno, $errmsg) = $this->assertADOdbError('metaPrimaryKeys()');

            $this->assertIsArray(
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] metaPrimaryKeys should return an array',
                    $fetchModeName
                )
            );

            if (!is_array($executionResult)) {
                continue;
            }

            $this->assertCount(
                2,
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] Checking Primary Key Elements Count should be 2, got %d',
                    $fetchModeName,
                    count($executionResult)
                )
            );

            if (count($executionResult) != 2) {
                continue;
            }

            $this->assertSame(
                'id',
                $executionResult[0],
                sprintf(
                    '[FETCH MODE %s] Validating the first element of the primary key is on column ID, got %s',
                    $fetchModeName,
                    $executionResult[0]
                )
            );
        }
    }


    /**
     * Test for errors when a metaprimarykeys function is called on an invalid table
     *
     * @return void
     */
    public function testMetaPrimaryKeysForInvalidTable(): void
    {


        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $response = $this->db->metaPrimaryKeys('invalid_table');

            $this->assertFalse(
                $response,
                sprintf(
                    '[FETCH MODE %s] Checking that metaPrimaryKeys returns ' .
                    'false for an invalid table',
                    $fetchModeName
                )
            );
        }
    }
}
