<?php

/**
 * Tests cases for the IBM DB2 driver of ADOdb.
 * Try to write database-agnostic tests where possible.
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
 *
 */

namespace MNewnham\ADOdbUnitTest\Drivers;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class Db2DriverTest
 *
 * Test cases for the IBM DB2 driver of ADOdb
 */
class Db2DriverTest extends ADOdbTestCase
{
    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();

        if ($this->adoDriver !== 'db2') {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'This test is only applicable for the IBM db2 driver'
            );
        }
    }
}
