<?php

/**
 * Tests cases for the PDO driver of ADOdb.
 * the driver specific test, e.g. pdo-sqlite, pdo-mysql, etc are also run
 * Try to write database-agnostic tests where possible.
 * This test does not support the legacy PDO drivers
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
 * @link https://github.com/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

namespace MNewnham\ADOdbUnitTest\Drivers;

use MNewnham\ADOdbUnitTest\Drivers\ADOdbCustomDriver;

/**
 * Class PdoDriverTest
 *
 * Test cases for the ADOdb PDO Drivers
 */
class PdoDriverTest extends ADOdbCustomDriver
{
    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();

        if (substr($this->adoDriver, 0, 3) !== 'pdo') {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'This test is only applicable for PDO drivers'
            );
        }
    }

    /**
     * Tear down the test environment
     *
     * @return void
     */
    public function tearDown(): void
    {
    }
}
