<?php

/**
 * Tests cases for Dictionary Alter Column functions of ADODb
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
 * Class AlterColumnTest
 *
 * Test cases for for ADOdb DataDictAlterColumn
 */
class AlterColumnTest extends DataDictFunctions
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
     * Test for {@see ADODConnection::alterColumnSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:altercolumnsql
     *
     * @return void
     */
    public function testalterColumnInBasicTable(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not created successfully'
            );
            return;
        }

        if ($this->adoDriver == 'sqlite3') {
            $this->markTestSkipped(
                'Skipping test as AlterColumnSql not currently supported by SQLite driver'
            );
            return;
        }


        $tableName = $this->testTableName;

        $metaColumns = $this->db->metaColumns($tableName);

        $flds = " 
            VARCHAR_FIELD VARCHAR(120)
            ";

        $sqlArray = $this->dataDictionary->alterColumnSQL(
            $tableName,
            $flds
        );

        if (count($sqlArray) == 0) {
            $this->fail(
                'AlterColumnSql() not supported currently by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        /*
        * re-read the column definitions
        */
        $metaColumns = $this->db->metaColumns($tableName);

        $this->assertArrayHasKey(
            'VARCHAR_FIELD',
            $metaColumns,
            'AlterColumnSQL should not remove the VARCHAR_FIELD from the table'
        );

        $this->assertSame(
            '120',
            (string)$metaColumns['VARCHAR_FIELD']->max_length,
            'AlterColumnSQL should have Increased the ' .
            'length of VARCHAR_FIELD to from 50 to 120'
        );

        $flds = " 
            INTEGER_FIELD I8 NOTNULL DEFAULT 1
            ";

        $sqlArray = $this->dataDictionary->alterColumnSQL(
            $tableName,
            $flds
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }
        /*
        * re-read the column definitions
        */
        $metaColumns = $this->db->metaColumns($tableName);

        $this->assertArrayHasKey(
            'INTEGER_FIELD',
            $metaColumns,
            'AltercolumnSQL INTEGER_FIELD should still exist in the table'
        );

        $this->assertSame(
            '1',
            $metaColumns['INTEGER_FIELD']->default_value,
            'AltercolumnSql should have change the default ' .
            'of INTEGER_FIELD from 0 to 1'
        );

        /*
        * Change the scale of the decimal field
        */

         $flds = " 
            DECIMAL_FIELD_TO_MODIFY N(16.12) NOTNULL
            ";

        $sqlArray = $this->dataDictionary->alterColumnSQL(
            $tableName,
            $flds
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        /*
        * re-read the column definitions
        */
        $metaColumns = $this->db->metaColumns($tableName);

        $this->assertArrayHasKey(
            'DECIMAL_FIELD_TO_MODIFY',
            $metaColumns,
            'AltercolumnSQL DECIMAL_FIELD_TO_MODIFY should still exist in the table'
        );

        $this->assertSame(
            '16',
            $metaColumns['DECIMAL_FIELD_TO_MODIFY']->max_length,
            'AlterColumnSQL: maxlength of DECIMAL_FIELD_TO_MODIFY' .
            'should have changed from 8 to 16'
        );

        $this->assertSame(
            '12',
            $metaColumns['DECIMAL_FIELD_TO_MODIFY']->scale,
            'AlterColumnSQL: Change of scale of DECIMAL_FIELD_TO_MODIFY 4 to 12'
        );
    }
}
