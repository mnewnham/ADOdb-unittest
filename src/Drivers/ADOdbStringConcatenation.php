<?php

/**
 * Base Tests cases for custom drivers
 * Try to write database-agnostic tests where possible.
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

namespace MNewnham\ADOdbUnitTest\Drivers;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class ADOdbCustomDriver
 * Base Class for custom driver tests
 */

class ADOdbStringConcatenation extends ADOdbTestCase
{

    /**
     * How the driver formats the concatenated data;
     *
     * @var string
     */
    protected string $concatenationFormat = '%s+%s';

    /**
     * Test for {@see ADODConnection::concat()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:concat
     *
     * @return void
     */
    #[DataProvider('providerTestConcat')]
    public function testConcat(int $fetchMode, string $firstColumn, string $secondColumn): void
    {

        /*
        * Find a record that has a varchar_field value
        */

        $this->db->setFetchMode($fetchMode);

        $sql = "SELECT number_run_field, varchar_field 
                  FROM testtable_1 
                 WHERE varchar_field IS NOT NULL";


        $row = $this->db->getRow($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $expectedValue = sprintf(
            $this->concatenationFormat,
            $row[$secondColumn],
            $row[$secondColumn]
        );

        $field = $this->db->Concat('varchar_field', "'|'", 'varchar_field');
        list($errno, $errmsg) = $this->assertADOdbError('concat()');

        $sql = "SELECT $field 
                  FROM testtable_1 
                 WHERE number_run_field={$row[$firstColumn]}";


        $result = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $expectedValue,
            $result,
            sprintf('3 value concat should return %s', $expectedValue)
        );

        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }

    /**
     * Data provider for {@see testConcat()}
     *
     * @return array [int $fetchmode, string $number_run_column, string $varchar_column]
     */
    public static function providerTestConcat(): array
    {

        switch (ADODB_ASSOC_CASE) {
            case ADODB_ASSOC_CASE_UPPER:
                return [
                'FETCH_ASSOC, ASSOC_CASE_UPPER' =>
                array(
                    ADODB_FETCH_ASSOC,
                    'NUMBER_RUN_FIELD',
                    'VARCHAR_FIELD',
                ),
                'FETCH_NUM,ASSOC_CASE_UPPER' =>
                array(
                    0 => ADODB_FETCH_NUM,
                    1 => "0",
                    2 => "1"

                )
            ];
            break;

            case ADODB_ASSOC_CASE_LOWER:
            default:
                return [
                'FETCH_ASSOC, ASSOC_CASE_LOWER' => [
                    ADODB_FETCH_ASSOC,
                    'number_run_field',
                    'varchar_field',
                ],
                'FETCH_NUM, ASSOC_CASE_UPPER' => [
                    0 => ADODB_FETCH_NUM,
                    1 => "0",
                    2 => "1"
                ]
            ];

            break;
        }
    }
}
