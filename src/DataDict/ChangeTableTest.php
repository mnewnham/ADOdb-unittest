<?php

/**
 * Tests cases for DataDictChangeTable functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\DataDict;

use MNewnham\ADOdbUnitTest\DataDict\DataDictFunctions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class ChangeTableTest
 *
 * Test cases for for ADOdb DataDictChangeTable
 */
class ChangeTableTest extends DataDictFunctions
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
    }

    /**
     * Test for {@see ADODConnection::changeTableSQL()} retaining Dropped Fields
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:changetablesql
     *
     * @return void
     */
    public function testChangeTableRetainDrops(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not ' .
                'created successfully'
            );
            return;
        }

        $metaColumns = $this->db->metaColumns($this->testTableName);

        /*
        * Changes:
        * Length of varchar field from 50 to 80
        * changes default value of date_field from '2030-01-01' to '2010-01-01'
        * date_field also exists as DATE_FIELD for metacolumns
        * Adds another_integer_field
        * Adds yet_another_integer_field
        * Changes decimal_field_to_modify from 8.4 to 9.5 and changes default
        */

        $flds = " 
            VARCHAR_FIELD C(80) NOTNULL DEFAULT '',
            NVARCHAR_FIELD C2(80) NOTNULL DEFAULT '',
            date_field D NOTNULL DEFAULT '2010-01-01',
            ANOTHER_INTEGER_FIELD I NOTNULL DEFAULT 0,
            BOOLEAN_FIELD_TO_CHANGE_DEFAULT L DEFAULT 0,
            YET_ANOTHER_VARCHAR_FIELD C2(50) NOTNULL DEFAULT '',
            DECIMAL_FIELD_TO_MODIFY N(9.5) NOTNULL DEFAULT 1,
            SMALLINT_TO_EXPAND I4,
            XL_FIELD XL,
            ";

        $sqlArray = $this->dataDictionary->changeTableSQL(
            $this->testTableName,
            $flds
        );

        $this->assertIsArray(
            $sqlArray,
            'changeTableSql() should alway return an array'
        );

        if (count($sqlArray) == 0) {
            $this->fail(
                'changeTableSql() not supported by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }


        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayHasKey(
            'DROPPABLE_FIELD',
            $metaColumns,
            '[changeTableSql]  using $dropflds=false ' .
            '- old column DROPPABLE_FIELD should be retained even if ' .
            'not in the new definition'
        );

        $this->assertArrayHasKey(
            'ANOTHER_INTEGER_FIELD',
            $metaColumns,
            '[changeTableSql] ANOTHER_INTEGER_FIELD should have been added'
        );


        $this->assertArrayHasKey(
            'YET_ANOTHER_VARCHAR_FIELD',
            $metaColumns,
            '[changeTableSql] YET_ANOTHER_VARCHAR_FIELD should have been added'
        );

        if (!array_key_exists('ANOTHER_VARCHAR_FIELD', $metaColumns)) {
            $this->skipFollowingTests = true;
        }

        /*
        * Changes:
        * Length of varchar field from 50 to 80
        * changes default value of date_field from '2030-01-01' to '2010-01-01'
        * Adds snother_integer_field
        * Adds yest_another_integer_field
        * Changes decimal_field_to_modify from 8.4 to 9.5 and changes default
        */

        $this->assertSame(
            80,
            $metaColumns['VARCHAR_FIELD']->max_length,
            '[changeTableSql] VARCHAR_FIELD should have increased length from 50 to 80'
        );

        $dbdate = str_replace("'", '', $this->db->dbDate('2010-01-01'));

        $this->assertSame(
            $dbdate,
            $metaColumns['DATE_FIELD']->default_value,
            '[changeTableSql] DATE_FIELD should have changed default from 2030-01-01 to 2010-01-01'
        );

        $this->assertSame(
            9,
            $metaColumns['DECIMAL_FIELD_TO_MODIFY']->precision,
            '[changeTableSql] DECIMAL_FIELD_TO_MODIFY should have changed max length from 8 to 9'
        );

        $this->assertSame(
            5,
            $metaColumns['DECIMAL_FIELD_TO_MODIFY']->scale,
            '[changeTableSql] DECIMAL_FIELD_TO_MODIFY should have changed max length from 4 to 5'
        );

        $this->assertSame(
            sprintf('%9.5f', 1),
            sprintf('%9.5f', $metaColumns['DECIMAL_FIELD_TO_MODIFY']->default_value),
            '[changeTableSql] DECIMAL_FIELD_TO_MODIFY should have changed default from 0 to 1'
        );

        /*
        * Now re-execute wth the drop flag set to true
        */
        $sqlArray = $this->dataDictionary->changeTableSQL(
            $this->testTableName,
            $flds,
            false,
            true
        );


        $assertion = $this->assertIsArray(
            $sqlArray,
            'changeTableSql() should alway return an array'
        );

        if (!$assertion) {
            return;
        }

        if (count($sqlArray) == 0) {
            $this->fail(
                'changeTableSql() not supported by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayNotHasKey(
            'INTEGER_FIELD',
            $metaColumns,
            'changeTableSQL() using $dropFlds=true ' .
            'old column INTEGER_FIELD should be dropped'
        );
    }

    /**
     * Test for {@see ADODConnection::changeTableSQL()} Dropping unspecified Fields
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:changetablesql
     *
     * @return void
     */
    public function testChangeTableCompleteDrops(): void
    {

        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not ' .
                'created successfully'
            );
            return;
        }

        /*
        * When testing droppable action, all the other fields except
        * the one to drop must be defined, else they will be dropped too
        */
        $flds = " 
            ID I AUTO PRIMARY,
            INTEGER_FIELD I NOTNULL DEFAULT 0,
            VARCHAR_FIELD C(80) NOTNULL DEFAULT '',
            NVARCHAR_FIELD C2(80) NOTNULL DEFAULT '',
            DATE_FIELD D NOTNULL DEFAULT '2010-01-01',
            BOOLEAN_FIELD_TO_RENAME L DEFAULT 0 NOTNULL
              BOOLEAN_FIELD_TO_CHANGE_DEFAULT L DEFAULT 0,
            ANOTHER_INTEGER_FIELD I NOTNULL DEFAULT 0,
            YET_ANOTHER_VARCHAR_FIELD C2(50) NOTNULL DEFAULT '',
            DECIMAL_FIELD_TO_MODIFY N(9.5) NOTNULL DEFAULT 1
            ";

        $sqlArray = $this->dataDictionary->changeTableSQL(
            $this->testTableName,
            $flds,
            false,
            true
        );

        $this->assertIsArray(
            $sqlArray,
            'changeTableSql() should alway return an array'
        );

        if (count($sqlArray) == 0) {
            $this->fail(
                'changeTableSql() not supported by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        return;

        $metaColumns = $this->db->metaColumns($this->testTableName);


        /*
        * Now re-execute wth the drop flag set to true
        */
        $sqlArray = $this->dataDictionary->changeTableSQL(
            $this->testTableName,
            $flds,
            false,
            true
        );


        $assertion = $this->assertIsArray(
            $sqlArray,
            'changeTableSql() should alway return an array'
        );

        if (!$assertion) {
            return;
        }

        if (count($sqlArray) == 0) {
            $this->fail(
                'changeTableSql() not supported by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayNotHasKey(
            'INTEGER_FIELD',
            $metaColumns,
            'changeTableSQL() using $dropFlds=true ' .
            'old column INTEGER_FIELD should be dropped'
        );
    }
}
