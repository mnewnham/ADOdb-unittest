<?php

/**
 * Global include for Helper Tests
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

namespace MNewnham\ADOdbUnitTest\Helpers;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class HelperFunctions
 *
 * Global include for Helper Tests
 */
class HelperFunctions extends ADOdbTestCase
{

    protected string $testTableName = 'testtable_3';


    /**
     * Set up the test environment first time
     *
     * @return void
     */
    public static function setupBeforeClass(): void
    {
        $db        = $GLOBALS['ADOdbConnection'];

        /*
        *load Data into the table, checking for driver specific loader
        */

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->startTrans();
        }

        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/autoexecute-schema.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        /*
        * Loads the schema based on the DB type
        */
        readSqlIntoDatabase($db, $tableSchema);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->completeTrans();
        }
    }

    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();
    }

}
