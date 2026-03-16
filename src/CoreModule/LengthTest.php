<?php

/**
 * Base Tests cases for cADOConnection::length()
 *
 * This file is part of ADOdb-unittest, a PHPUnit test suite for
 * the ADOdb Database Abstraction Layer library for PHP.s
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

use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class LengthTest
 * Tests db agnostic field length tests
 */
class LengthTest extends ADOdbTestCase
{

    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        $db = $GLOBALS['ADOdbConnection'];
        /*
        * Load the table to test data length tests
        */
        $schemaFile = sprintf(
            '%s/DatabaseSetup/%s/length-test.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );


        $db->startTrans();
        $ok = readSqlIntoDatabase($db, $schemaFile);
        $db->completeTrans();
    }

    /**
     * Test for {@see ADODConnection::Length()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:length
     *
     * @return void
     */
    public function testFieldLength(): void
    {
        
        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            
            $absoluteMode = $this->insertFetchMode($fetchMode);

            $metaColumns = $this->db->metaColumns('length_test');

            $lengthColumns = [];

            foreach($metaColumns as $col => $data) {
                $metaType = $this->db->metaType($data->type);
                if (!in_array($metaType, ['C','C2','X','B','XL'])) {
                    continue;
                }
                $lengthColumns[] = sprintf(
                    '%s %s_length',
                    $this->db->length(strtolower($data->name)),
                    strtolower($data->name)
                );
            }

            $lengthString = implode(',',$lengthColumns);


            $sql = "SELECT $lengthString
                      FROM length_test 
                 WHERE id=1";

            $row = $this->db->getRow($sql);
            list($errno, $errmsg) = $this->assertADOdbError($sql);

            $numericRow = [];

            if ($absoluteMode == ADODB_FETCH_BOTH) {
                $numericRow = array_filter($row, function($value, $key){
                    return is_numeric($key);
                },ARRAY_FILTER_USE_BOTH);
            } else {
                $numericRow = array_values($row);
            }

            foreach($numericRow as $k => $v) {
                $this->assertEquals(
                    20,
                    (int)$v,
                    sprintf(
                        '[FETCH %s] Test of length of column %s failed',
                        $fetchDescription,
                        $lengthColumns[$k]
                    )
                );
            }
        }    

    }

    /**
     * Test for {@see ADODConnection::Length()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:length
     *
     * @return void
     */
    public function testFieldValue(): void
    {
        
        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            
            $absoluteMode = $this->insertFetchMode($fetchMode);

            $metaColumns = $this->db->metaColumns('length_test');

            $valueColumns = [];

            foreach($metaColumns as $col => $data) {
                $metaType = $this->db->metaType($data->type);
                if (!in_array($metaType, ['C','C2','X','XL'])) {
                    continue;
                }
                $valueColumns[] = sprintf(
                    'TRIM(%s)',
                    strtolower($data->name)
                );
            }

            $valueString = implode(',',$valueColumns);


            $sql = "SELECT $valueString
                      FROM length_test 
                 WHERE id=1";

            $row = $this->db->getRow($sql);
            list($errno, $errmsg) = $this->assertADOdbError($sql);

            $numericRow = [];

            if ($absoluteMode == ADODB_FETCH_BOTH) {
                $numericRow = array_filter($row, function($value, $key){
                    return is_numeric($key);
                },ARRAY_FILTER_USE_BOTH);
            } else {
                $numericRow = array_values($row);
            }

            foreach($numericRow as $k => $v) {
                $this->assertEquals(
                    'TEST567890TEST567890',
                    $v,
                    sprintf(
                        '[FETCH %s] Test of value of column %s failed',
                        $fetchDescription,
                        $valueColumns[$k]
                    )
                );
            }
        }    

    }
}
