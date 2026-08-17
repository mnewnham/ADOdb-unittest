<?php

/**
 * Tests cases for MetaSequences functions of ADODb
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
class MetaSequencesTest extends MetaFunctions
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
     * @param mixed  $mask          Return numeric indexes
     * @param mixed   $schema        The fetch mode
     * @param mixed $expectedCount What should be returned
     *
     * @return void
     */
    #[DataProvider('providerTestMetaSequences')]
    public function testMetaSequences(
        mixed $mask,
        mixed $schema,
        mixed $expectedCount,
    ): void {

        if (in_array($GLOBALS['ADOdriver'], [ 'sqlite3', 'pdo-sqlite', 'mysqli', 'pdo-mysql' ])) {
            $this->markTestSkipped(
                'Driver does not natively support Sequences'
            );
            return;
        }

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->insertFetchMode($fetchMode);

            $executionResult = $this->db->metaSequences(
                $mask,
                $schema
            );

           // print_r($executionResult);

            //list($errno, $errmsg) = $this->assertADOdbError('metaSequences()');

            if (is_array($executionResult) && $expectedCount === 0) {
                $this->assertIsArray(
                    $executionResult,
                    sprintf(
                        '[FETCH %s] metaSequences() did not return an array of Sequences',
                        $fetchModeName
                    )
                );
            } else if ($expectedCount === false) {
           
                $this->assertSame(
                    $expectedCount,
                    $executionResult,
                    sprintf(
                        '[FETCH MODE: %s] Checking Result should be false',
                        $fetchModeName
                    )
                );
            } else {
           
                $this->assertSame(
                    $expectedCount,
                    count($executionResult),
                    sprintf(
                        '[FETCH MODE: %s] Checking Key Count of returned data',
                        $fetchModeName
                    )
                );
            }

            $this->validateResetFetchModes();
        }
    }

    /**
     * Data provider for {@see testMetaSequences()}
     *
     * @return array [bool array type, array return value]
     */
    public static function providerTestMetaSequences(): array
    {

        $schema = $GLOBALS['DriverControl']->schemaOwner;

        return [
            'Default Behavior No mask, no Schema' => [
                false,
                false,
                0
            ],
            'Unique Match On mask, no schema' => [
                'seq_test_1',
                false,
                1
            ],
             'Unique Match On mask, my schema' => [
                'seq_test_1',
                $schema,
                1
            ],
            'Wildcard Match On mask, no schema' => [
                'seq_test_%',
                false,
                2
            ],
            'No Match On Invalid name, no schema' => [
                'invalid_seq_test_1',
                false,
                false
            ],
            
        ];
    }

}
