<?php

/**
 * Tests cases for ServerInfo functions of ADODb
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
 * Class ServerInfoTest
 *
 * Test cases for for ADOdb ServerInfo
 */
class ServerInfoTest extends MetaFunctions
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
     * Test for {@see ADODConnection::serverInfo()]
     * Checks that version is returned
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:serverinfo
     *
     * @return void
     */
    public function testServerInfoVersion(): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $executionResult = $this->db->serverInfo();

            $this->assertIsArray(
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] serverInfo should return an array',
                    $fetchModeName
                )
            );

            if (!is_array($executionResult)) {
                return;
            }


            $this->assertArrayHasKey(
                'version',
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] Checking for mandatory key ' .
                    '"version" in serverInfo',
                    $fetchModeName
                )
            );
            $this->assertArrayHasKey(
                'description',
                $executionResult,
                sprintf(
                    '[FETCH MODE %s] Checking for mandatory key ' .
                    '"description" in serverInfo',
                    $fetchModeName
                )
            );
        }
    }
}
