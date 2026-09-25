<?php

/**
 * Tests cases for date functions of ADODb. Some of these functions
 * are effectively obsolete since 64 bit processors
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

use MNewnham\ADOdbUnitTest\DateFunctions\DateHandling;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class DateFunctions.
 *
 * Test cases for ADOdb date functions
 */
class BindTimestampTest extends DateHandling
{

    /**
     * Test for {@see ADOConnection::bindTimestamp())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:bindtimestamp
     *
     * @return void
     */
    public function testBindTimestamp(): void
    {
        $nowTime = time();
        $now = date('Y-m-d H:i:s', $nowTime);

        $dbTs = $this->db->bindTimestamp($now);

        if (substr($dbTs, 0, 1) != "'") {
            $this->fail(
                sprintf(
                    'bindTimestamp() should return a timestamp single quoted, actually returned[%s]',
                    $dbTs
                )
            );
        }

        list($errno, $errmsg) = $this->assertADOdbError('dbTimestamp()');

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $dbTs
        );

        $actual = $this->db->getOne($sql);

        $sql = 'SELECT * 
                  FROM date_columns_test 
                  WHERE datetime_field=' . sprintf($GLOBALS['DriverControl']->dateTimeTranslation, $dbTs);

        $result = $this->db->selectLimit($sql, 1, -1);

        $this->assertIsObject(
            $result,
            sprintf(
                "Execution of the SQL %s without parameters should have returned an ADOrecordset object",
                $sql
            )
        );

        if (!$r = $result->fetchRow()) {
            $this->assertTrue(
                true,
                'OK'
            );
        }

        
        if (!$r = $result->fetchRow()) {
            $this->assertTrue(
                true,
                'OK'
            );
        }
    }

    /**
     * Test for {@see ADOConnection::bindTimestamp())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:bindtimestamp
     *
     * @return void
     */
    public function testParameterizedBindTimestamp(): void
    {
        $nowTime = time();
        $now = date('Y-m-d H:i:s', $nowTime);

        $dbTs = $this->db->bindTimestamp($now);

        if (substr($dbTs, 0, 1) != "'") {
            $this->fail(
                sprintf(
                    'bindTimestamp() should return a timestamp single quoted, actually returned[%s]',
                    $dbTs
                )
            );
        }

        list($errno, $errmsg) = $this->assertADOdbError('dbTimestamp()');

        $sql = sprintf(
            $GLOBALS['DriverControl']->dateMethodExecutor,
            $dbTs
        );

        $actual = $this->db->getOne($sql);

        $this->db->param(false);
        $p1 = $this->db->param('p1');

        $bind = [ 'p1' => $dbTs ];

        $sql = 'SELECT * 
                  FROM date_columns_test 
                  WHERE datetime_field=' . sprintf($GLOBALS['DriverControl']->dateTimeTranslation, $p1);

        $result = $this->db->selectLimit($sql, 1, -1, $bind);

        $this->assertIsObject(
            $result,
            sprintf(
                "Execution of the SQL %s with parameters %s should have returned an ADOrecordset object",
                $sql,
                print_r($bind, true)
            )
        );

        if (!$r = $result->fetchRow()) {
            $this->assertTrue(
                true,
                'OK'
            );
        }
    }
}
