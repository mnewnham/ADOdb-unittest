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
            'datetime_field' => date('Y-m-d H:i:s'),
            'sqldate_test_field' => date('Y-m-d H:i:s'),
            'offsetdate_test_field' => date('Y-m-d H:i:s')
        ];

        $sql = $db->getInsertSql($template, $fields);

        $db->startTrans();
        $result = $db->execute($sql);
        $fields = [
            'date_field' => '1959-08-29',
            'datetime_field' => '1959-08-29 13:15:00',
            'sqldate_test_field' => '1959-08-29 13:15:00',
            'offsetdate_test_field' => '1959-08-29 13:15:00'
        ];

        $sql = $db->getInsertSql($template, $fields);

        $result = $db->execute($sql);
        $db->completeTrans();
    }

    /**
     * Determones if the actual date is between the seconds range
     * in which case, we assume the result was good
     *
     * @param string $expected The expected result
     * @param string $actual   The actual result
     * @param integer $margin  The number of seconds variance
     * 
     * @return array
     */
    protected function timeRangeHandling(
        string $expected, 
        string $actual, 
        int $margin
    ) : array {
        $result = [ 0, '' ];

        $success = 0;
        $message = '';

        $tExpected = strtotime($expected);
        $tActual   = strtotime($actual);

        if (preg_match('/^[0-9]+$/', $expected) && $actual && preg_match('/^[0-9]+$/', $actual)) {
            $range = [
                'from' => $expected - $margin,
                'to'   => $expected + $margin
            ];


            $success = ($actual >= $range['from'] && $actual <= $range['to'] ) ? true : false;
            $message = sprintf(
                'Range variance error - The value should be between %s and %s, actually %s',
                $range['from'],
                $range['to'],
                $actual
            );
        } else if (preg_match('/^[0-9]+$/', $tExpected) && $tActual && preg_match('/^[0-9]+$/', $tActual)) {
            $range = [
                'from' => $tExpected - $margin,
                'to'   => $tExpected + $margin
            ];


            $success = ($tActual >= $range['from'] && $tActual <= $range['to'] ) ? true : false;
            $message = sprintf(
                'Range variance error - The value should be between %s and %s, actually %s',
                date('Y-m-d H:i:s', $range['from']),
                date('Y-m-d H:i:s', $range['to']),
                $actual
            );
        } else {
            $success = strcmp($expected, $actual ?? '') !== false ? true : false;
            $message = sprintf(
                '$s - Expected %s, got %s',
                $message,
                $expected .
                $actual
            );
        }

        $result[0] = $success;
        if (!$success) {
            $result[1] = $message;
        }
        return $result;
    }
}
