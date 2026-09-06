<?php

/**
 * Base Test class for Date handling functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\DateFunctions;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaFunctions
 *
 * Test cases for for ADOdb MetaFunctions
 */
class DateHandling extends ADOdbTestCase
{

    /**
     * Sets up a flag used from refreshing the table mid-test
     *
     * @return void
     */
    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();
        $GLOBALS['ADOdbConnection']->_errorCode = 0;
        $db = $GLOBALS['ADOdbConnection'];
    
        /*
        * Refreshes the schema at the start of each process
        */
        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/date-columns-test.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        $success = readSqlIntoDatabase($db, $tableSchema);

        $db->startTrans();
        $db->execute('TRUNCATE TABLE date_columns_test');
        $template = $db->execute('SELECT * FROM date_columns_test WHERE id=-1');

        $fields = [
            'date_field' => date('Y-m-d'),
            'datetime_field' => date('Y-m-d H:i'),
            'sqldate_test_field' => date('Y-m-d H:i'),
            'offsetdate_test_field' => date('Y-m-d H:i')
        ];

        $sql = $db->getInsertSql($template, $fields);

        $db->startTrans();
        $result = $db->execute($sql);
        $fields = [
            'date_field' => '1959-08-29',
            'datetime_field' => '1959-08-29 13:15',
            'sqldate_test_field' => '1959-08-29 13:15',
            'offsetdate_test_field' => '1959-08-29 13:15'
        ];

        $sql = $db->getInsertSql($template, $fields);
        $result = $db->execute($sql);
        $db->completeTrans();
    }

}
