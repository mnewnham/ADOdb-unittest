<?php

/**
 * Tests cases for core SQL functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\CoreModule;

/**
 * ClassGetOneTest
 *
 * Test cases for for ADOdb Core functions
 */
class GetOneTest extends ADOdbCoreSetup
{
    /**
     * Test for {@see ADODConnection::getOne()]
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:getone
     *
     * @param string $expectedValue The expected value to be returned
     * @param string $sql The SQL query to execute
     * @param ?array $bind An optional array of bind parameters
     *
     * @return void
     *
     * @dataProvider providerTestGetOne
     */
    public function testGetOne(string $expectedValue, string $sql, ?array $bind): void
    {

        if ($bind) {
            $this->db->startTrans();
            $actualValue = (string)$this->db->getOne($sql, $bind);

            list($errno,$errmsg) = $this->assertADOdbError($sql, $bind);
            $this->db->completeTrans();

            $this->assertSame(
                $expectedValue,
                $actualValue,
                'Test of getOne with bind variables'
            );
        } else {
            $this->db->startTrans();
            $actualValue = (string)$this->db->getOne($sql);

            list($errno,$errmsg) = $this->assertADOdbError($sql);
            $this->db->completeTrans();

            $this->assertSame(
                $expectedValue,
                $actualValue,
                'Test of getOne without bind variables'
            );
        }
    }

    /**
     * Data provider for {@see testGetOne()}
     *
     * @return array [string expected value, string sql ?array bind]
     */
    static function providerTestGetOne(): array
    {
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 9);

        return [
             'Return First Col, Unbound' => [
                '9',
                "SELECT number_run_field 
                   FROM testtable_3  
                  WHERE number_run_field < 10
               ORDER BY number_run_field DESC",
               null
                ],
                'Return Multiple Cols, take first, Unbound' => [
                'LINE 9',
                "SELECT testtable_3.varchar_field,testtable_3.* 
                   FROM testtable_3 
                  WHERE number_run_field < 10
               ORDER BY number_run_field DESC", null],
                'Return Multiple Cols, take first, Bound' => [
                'LINE 9',
                "SELECT testtable_3.varchar_field,testtable_3.* 
                   FROM testtable_3 
                  WHERE number_run_field=$p1",
                  $bind
                ],

            ];
    }
}
