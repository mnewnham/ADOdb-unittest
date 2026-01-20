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
 * @copyright 2025 Mark Newnham, Damien Regad and the ADOdb community
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 *
 * @link https://github.com/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

use MNewnham\ADOdbUnitTest\Drivers\ADOdbCustomDriver;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
//#[RequiresPhpExtension('mysqli')]
class MysqliDriverTest extends ADOdbCustomDriver
{
    protected mixed $physicalType;
    protected ?string $columnType;

    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        if (!array_key_exists('xmlschema', $GLOBALS['TestingControl'])) {
            return;
        }

        $GLOBALS['ADOdbConnection']->startTrans();
        $GLOBALS['ADOdbConnection']->execute("DROP TABLE IF EXISTS testxmltable_1");
        $GLOBALS['ADOdbConnection']->completeTrans();
    }
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

        $this->physicalType = MYSQLI_TYPE_JSON;
        $this->columnType   = 'JSON';
    }
}
