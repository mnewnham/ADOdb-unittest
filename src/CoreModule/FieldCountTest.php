<?php

/**
 * Tests cases for FieldCount of ADODb
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

namespace MNewnham\ADOdbUnitTest\CoreModule;

use MNewnham\ADOdbUnitTest\CoreModule\ADOdbCoreSetup;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class FieldCount
 *
 * Test cases for for ADOdb recordCount
 */
class FieldCountTest extends ADOdbCoreSetup
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        $db        = $GLOBALS['ADOdbConnection'];
    }

    /**
     * Test update against an unbound statement
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testFieldCountWithoutBind(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $this->insertFetchMode($fetchMode);

        $SQL = "SELECT * FROM testtable_1 WHERE id=-1";
        $result = $this->db->execute($SQL);

        $this->assertEquals(
            9,
            $result->fieldCount(),
            sprintf('[FETCH %s] FieldCount shoud return 9 with no bind usage', $fetchDescription)
        );
    }

    /**
     * Test count against bound update
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testFieldCountWithBind(
        int $fetchMode,
        string $fetchDescription
    ): void {


        $this->insertFetchMode($fetchMode);

        $p1 = $this->db->param('p1');

        $bind = ['p1' => -1];

        $SQL = "SELECT * FROM testtable_1 WHERE id=$p1";
        $result = $this->db->execute($SQL, $bind);

        $this->assertEquals(
            9,
            $result->fieldCount(),
            sprintf('[FETCH %s] FieldCount shoud return 9 with bind usage', $fetchDescription)
        );
    }
}
