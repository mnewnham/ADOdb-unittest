<?php

/**
 * Base Tests cases for ADOConnection OrderBy
 * based on original LibTest
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

class OrderByTest extends ADOdbTestCase
{
    /**
     * Test for {@see ADODConnection::orderBy()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:ifnull
     *
     * @return void
     */
    #[DataProvider('providerStripOrderBy')]
    public function testStripOrderBy($sql, $stripped): void
    {
        $this->assertSame($stripped, adodb_strip_order_by($sql));
    }

    /**
     * Data provider for {@see testIfnull()}
     *
     * @return array [int fetchmode, string number_run column, string date column]
     */
    /**
     * Data provider for {@see testStripOrderBy()}
     *
     * @return array [SQL statement, SQL with ORDER BY clause stripped]
     */
    public static function providerStripOrderBy(): array
    {
        return [
            'No order by clause' => [
                "SELECT name FROM table",
                "SELECT name FROM table"
            ],
            'Simple order by clause' => [
                "SELECT name FROM table ORDER BY name",
                "SELECT name FROM table"
            ],
            'Order by clause descending' => [
                "SELECT name FROM table ORDER BY name DESC",
                "SELECT name FROM table"
            ],
            'Order by clause with limit' => [
                "SELECT name FROM table ORDER BY name LIMIT 5",
                "SELECT name FROM table LIMIT 5"
            ],
            'Ordered Subquery with outer order by' => [
                "SELECT * FROM table WHERE name IN (SELECT TOP 5 name FROM table_b ORDER by name) ORDER BY name DESC",
                "SELECT * FROM table WHERE name IN (SELECT TOP 5 name FROM table_b ORDER by name)"
            ],
            'Ordered Subquery without outer order by' => [
                "SELECT * FROM table WHERE name IN (SELECT TOP 5 name FROM table_b ORDER by name)",
                "SELECT * FROM table WHERE name IN (SELECT TOP 5 name FROM table_b ORDER by name)"
            ],
        ];
    }

    /**
     * Test for {@see _adodb_quote_fieldname()}
     *
     * @dataProvider quoteProvider
     */
    #[DataProvider('quoteProvider')]
    public function testQuoteFieldNames(
        mixed $method,
        string $field,
        string $expected
    ): void {

        global $ADODB_QUOTE_FIELDNAMES;
        $ADODB_QUOTE_FIELDNAMES = $method;
        $this->assertSame(
            $expected,
            _adodb_quote_fieldname($this->db, $field)
        );
    }

    /**
     * Data provider for {@see testQuoteFieldNames()}
     * @return array
     */
    public static function quoteProvider()
    {
        $FIELD = sprintf(
            "%sFIELD%s",
            $GLOBALS['ADOdbConnection']->nameQuote,
            $GLOBALS['ADOdbConnection']->nameQuote
        );

        $field = strtolower($FIELD);

        $Field = str_replace('f', 'F', $field);

        $FIELDNAME = sprintf(
            "%sFIELD NAME%s",
            $GLOBALS['ADOdbConnection']->nameQuote,
            $GLOBALS['ADOdbConnection']->nameQuote
        );
        $fieldname = strtolower($FIELD);

        return [
            'No quoting, single-word field name' => [false, 'Field', 'FIELD'],
            'No quoting, field name with space' => [false, 'Field Name', "$FIELDNAME"],
            'Quoting `true`' => [true, 'Field', $FIELD],
            'Quoting `UPPER`' => ['UPPER', 'Field', $FIELD],
            'Quoting `LOWER`' => ['LOWER', 'Field', $field],
            'Quoting `NATIVE`' => ['NATIVE', 'Field', $Field],
            'Quoting `BRACKETS`' => ['BRACKETS', 'Field', '[FIELD]'],
            'Unknown value defaults to UPPER' => ['XXX', 'Field', $FIELD],
        ];
    }
}
