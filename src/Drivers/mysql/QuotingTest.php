<?php

/**
 * Tests cases for the mysqli driver of ADOdb.
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
 * @copyright 2025,2026 Mark Newnham
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 *
 * @link https://github.com/mnewnham/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

namespace MNewnham\ADOdbUnitTest\Drivers\mysql;

use MNewnham\ADOdbUnitTest\Drivers\ADOdbStringQuoting;

/**
 * Class MysqliDriverTest
 *
 * Test cases for for the MySQLi native driver
 */
class QuotingTest extends ADOdbStringQuoting
{
     

    /**
     * The expected result from the qstr test which has
     * database-specific escaping. This is a reasonable default
     *
     * @var     string $qStrExpectedResult
     */
    protected string $qStrExpectedResult = "Famed author James O\\'Sullivan";

    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();

        if ($this->adoDriver !== 'mysqli') {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'This test is only applicable for the mysqli driver'
            );
        }

    }


}
