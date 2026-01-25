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

use MNewnham\ADOdbUnitTest\CoreModule\ADOdbCoreSetup;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb Core functions
 */
class ExecuteTest extends ADOdbCoreSetup
{
    /**
     * Test for {@see ADODConnection::execute() in select mode]
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     *
     * @param bool $expectedValue
     * @param string $sql
     * @param ?array $bind
     *
     * @return void
     */
    #[DataProvider('providerTestSelectExecute')]
    public function testSelectExecute(bool $expectedValue, string $sql, ?array $bind): void
    {


        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {
            $this->db->setFetchMode($fetchMode);
            list($result,$errno,$errmsg) = $this->executeSqlString($sql, $bind);

            $this->assertSame(
                $expectedValue,
                is_object($result),
                sprintf(
                    '[%s] ADOConnection::execute() in SELECT mode',
                    $fetchDescription
                )
            );
        }
    }

    /**
     * Data provider for {@see testSelectExecute()}
     *
     * @return array [bool success, string sql ?array bind]
     */
    public static function providerTestSelectExecute(): array
    {
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 'LINE 1');
        return [
            'Select Unbound' => [
                true,
                "SELECT * FROM testtable_3 ORDER BY id",
                null
            ],
            /*
            'Invalid' => [
                false,
                "SELECT testtable_3.varchar_fieldx FROM testtable_3 ORDER BY id",
                null
            ],
            */
            'Select, Bound' => [
                true,
                "SELECT testtable_3.varchar_field,testtable_3.* FROM testtable_3 WHERE varchar_field=$p1",
                $bind
            ],
        ];
    }

    /**
     * Test for {@see ADODConnection::execute() in non-seelct mode]
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:execute
     *
     * @param bool $expectedValue
     * @param string $sql
     * @param ?array $bind
     *
     * @return void
     */
    #[DataProvider('providerTestNonSelectExecute')]
    public function testNonSelectExecute(bool $expectedValue, string $sql, ?array $bind): void
    {

        list($result,$errno,$errmsg) = $this->executeSqlString($sql, $bind);

        $this->assertSame(
            $expectedValue,
            is_object($result) && get_class($result) == 'ADORecordSet_empty',
            'ADOConnection::execute() in INSERT/UPDATE/DELETE mode ' .
            'should return an ADORecordSet_empty object'
        );
    }

    /**
     * Data provider for {@see testNonSelectExecute()}
     *
     * @return array [string success, string sql, ?array bind]
     */
    public static function providerTestNonSelectExecute(): array
    {
        $p1 = $GLOBALS['ADOdbConnection']->param('p1');
        $bind = array('p1' => 'LINE 1');
        return [
            'Update Unbound' => [
                true,
                "UPDATE testtable_3 SET integer_field=2000 WHERE id=1",
                null
            ],
            /*
            'Invalid' => [
                false,
                "UPDATE testtable_3 SET xinteger_field=2000 WHERE id=1",
                null
            ],
            */
            'Select, Bound' => [
                true,
                "UPDATE testtable_3 SET integer_field=2000 WHERE varchar_field=$p1",
                $bind
            ],
        ];
    }
}
