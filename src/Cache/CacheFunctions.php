<?php

/**
 * Global include for Cache Tests
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

namespace MNewnham\ADOdbUnitTest\Cache;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class HelperFunctions
 *
 * Global include for Helper Tests
 */
class CacheFunctions extends ADOdbTestCase
{

    protected string $testTableName = 'testtable_3';


    protected $cacheMethod = 0;
    protected $timeout     = 120;


    /**
     * Set up the test environment
     *
     * @return void
     */
    public static function setupBeforeClass(): void
    {

        //parent::setUpBeforeClass();

        $db        = &$GLOBALS['ADOdbConnection'];

        if (!isset($GLOBALS['TestingControl']['caching'])) {
             return;
        }

        $db->startTrans();
        $SQL = "SELECT COUNT(*) AS cache_table3_count FROM testtable_3";
        $table3DataExists = $db->getOne($SQL);

        $db->completeTrans();
        if ($table3DataExists) {
            // Data already exists, no need to reload
            return;
        }

        /*
        *load Data into the table, checking for driver specific loader
        */
        $db->startTrans();

        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/table3-data.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        if (!file_exists($tableSchema)) {
            $tableSchema = sprintf(
                '%s/DatabaseSetup/table3-data.sql',
                $GLOBALS['unitTestToolsDirectory']
            );
        }

        /*
        * Loads the schema based on the DB type
        */
        readSqlIntoDatabase($db, $tableSchema);

        $db->completeTrans();
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

    /**
     * Set the empty_field column to a value
     *
     * @param string $value Value to set the empty_field column to
     *
     * @return void
     */
    public function setEmptyColumn($value): void
    {

        if (!$value) {
            $value = 'NULL';
        } else {
            $value = $this->db->qstr($value);
        }

        $sql = "UPDATE testtable_3 SET empty_field = $value";
        list($result, $errno, $errmsg) = $this->executeSqlString($sql);
    }

}
