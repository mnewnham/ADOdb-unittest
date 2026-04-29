<?php

/**
 * Tests cases for MetaDatabases functions of ADODb
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
 * Class MetaDatabasesTest
 *
 * Test cases for for ADOdb MetaDatabases
 */
class MetaDatabasesTest extends MetaFunctions
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

    #[DataProvider('globalProviderFetchModes')]
    public function testMetaDatabases(
        int $fetchMode,
        string $fetchDescription
    ): void {


        if ($GLOBALS['DriverControl']->databaseIsSchema) {
            $baseDatabaseName = $this->db->user;
        } else {
            $baseDatabaseName = $this->db->database;
        }

        $this->insertFetchMode($fetchMode);

        $response = $this->db->metaDatabases();

        $this->validateResetFetchModes();

        /*
        $this->assertSame(
            $baseDatabaseName,
            $this->db->database,
            sprintf(
                '[FETCH MODE %s] Checking that metaDatabases ' .
                'resets the value of ADOConnection::database',
                $fetchDescription
            )
        );
        */

        $this->assertIsArray(
            $response,
            sprintf(
                '[FETCH MODE %s] Checking that metaDatabases ' .
                'returns an array of attached databases',
                $fetchDescription
            )
        );


        $flipResponse = array_change_key_case(array_flip($response), CASE_LOWER);

        $this->assertArrayHasKey(
            strtolower($baseDatabaseName ?? ''),
            $flipResponse,
            sprintf(
                '[FETCH MODE %s] Checking that metaDatabases ' .
                'returns the currently attached database',
                $fetchDescription
            )
        );
    }
}
