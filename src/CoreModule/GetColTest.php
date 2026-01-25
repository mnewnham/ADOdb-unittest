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
 * @link https://github.com/mnewnham/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

namespace MNewnham\ADOdbUnitTest\CoreModule;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class GetColTest
 *
 * Test cases for ADOdb Core functions
 */
class GetColTest extends ADOdbCoreSetup
{
    /**
     * Test for {@see ADODConnection::getCol()]
     *
     * @param mixed  $expectedValue The expected response
     * @param string $sql           The SQL to execute
     * @param ?array $bind          Optional Bind
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:getcol
     */
    #[DataProvider('providerTestGetCol')]
    public function testGetCol(mixed $expectedValue, string $sql, ?array $bind): void
    {

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->db->setFetchMode($fetchMode);

            $this->db->startTrans();
            if ($bind) {
                $cols = $this->db->getCol($sql, $bind);

                list($errno,$errmsg) = $this->assertADOdbError($sql, $bind);

                $this->assertSame(
                    $expectedValue,
                    count($cols),
                    sprintf(
                        '[%s] Get col with bind variables should return' .
                        'expected number of rows',
                        $fetchDescription
                    )
                );
            } else {
                $cols = $this->db->getCol($sql);

                list($errno,$errmsg) = $this->assertADOdbError($sql);
                $this->assertSame(
                    $expectedValue,
                    count($cols),
                    sprintf(
                        '[%s] getCol without bind variables should return ' .
                        'expected number of rows',
                        $fetchDescription
                    )
                );
            }
            $this->db->completeTrans();
        }
    }
    /**
     * Data provider for {@see testGetCol`()}
     *
     * @return array [string(getRe, array return value]
     */
    public static function providerTestGetCol(): array
    {
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 'LINE 11');
        return [
            [
                11,
                "SELECT varchar_field 
                   FROM testtable_3 
               ORDER BY id",
                    null
            ],[
                1,
                "SELECT testtable_3.varchar_field,testtable_3.* 
                   FROM testtable_3 
                  WHERE varchar_field=$p1",
                $bind
            ],
            [
                0,
                "SELECT testtable_3.varchar_field,testtable_3.* 
                   FROM testtable_3 
                  WHERE integer_field=-999
                    AND varchar_field=$p1",
                $bind
            ],
        ];
    }
}
