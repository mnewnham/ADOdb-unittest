<?php

/**
 * Base Tests cases for cADOConnection::ifNull
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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class ADOdbCustomDriver
 * Base Class for custom driver tests
 */

class IfNullTest extends ADOdbTestCase
{
    /**
     * Test for {@see ADODConnection::ifNull()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:ifnull
     *
     * @return void
     */
    #[DataProvider('providerTestIfnull')]
    public function testIfNull(int $fetchMode, string $firstColumn, string $secondColumn): void
    {

        $this->db->setFetchMode($fetchMode);

        $sql = "SELECT number_run_field, decimal_field 
                  FROM testtable_1 
                 WHERE date_field IS NOT NULL";

        $row = $this->db->getRow($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        /*
        * Set up a test record that has a NULL value
        */
        $sql = "UPDATE testtable_1 
                   SET decimal_field = null 
                 WHERE number_run_field={$row[$firstColumn]}";

        list($result, $errno, $errmsg) = $this->executeSqlString($sql);
        if ($errno > 0) {
            return;
        }

        /*
        * Now get a weird value back from the ifnull function
        */

        $sql = "SELECT {$this->db->ifNull('decimal_field', 8675304)} 
                  FROM testtable_1 
                 WHERE number_run_field={$row[$firstColumn]}";

        $expectedResult = (float)$this->db->getOne($sql);

        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            8675304,
            $expectedResult,
            'Test of ifnull function  should return 8675304'
        );

        /*
        * Reset the date_field to a non-null value
        */
        $sql = "UPDATE testtable_1 
                   SET decimal_field = {$row[$secondColumn]} 
                 WHERE number_run_field={$row[$firstColumn]}";

        list($result, $errno, $errmsg) = $this->executeSqlString($sql);

        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }

    /**
     * Data provider for {@see testIfnull()}
     *
     * @return array [int fetchmode, string number_run column, string date column]
     */
    public static function providerTestIfnull(): array
    {


        switch (ADODB_ASSOC_CASE) {
            case ADODB_ASSOC_CASE_UPPER:
                return [
                'FETCH_ASSOC,ASSOC_CASE_UPPER' => [
                    ADODB_FETCH_ASSOC,
                    'NUMBER_RUN_FIELD',
                    'DECIMAL_FIELD',
                ],
                'FETCH_NUM,ASSOC_CASE_UPPER' => [
                    0 => ADODB_FETCH_NUM,
                    1 => "0",
                    2 => "1"

                ]
            ];
            break;
            case ADODB_ASSOC_CASE_LOWER:
            default:
                return [
                'FETCH_ASSOC,ASSOC_CASE_LOWER' => [
                    ADODB_FETCH_ASSOC,
                    'number_run_field',
                    'decimal_field',
                ],
                'FETCH_NUM,ASSOC_CASE_UPPER' => [
                    0 => ADODB_FETCH_NUM,
                    1 => "0",
                    2 => "1"

                ]
            ];
            break;
        }
    }
}
