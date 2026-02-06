<?php

/**
 * Tests cases for DataDictDuplicateColumn functions of ADODb
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
 * Class DataDictDuplicateColumnTest
 *
 * Test cases for for ADOdb DataDictDuplicateColumn
 */
class DataDictDuplicateColumnTest extends DataDictFunctions
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
    }/**
     * Test for {@see ADODConnection::addColumnSQL()} adding a duplicate column with different case
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:addcolumnsql
     *
     * @return void
     */
    public function testAddDuplicateCasedColumn(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not created successfully'
            );
            return;
        }

        $tableName = $this->testTableName;

        $metaColumns = $this->db->metaColumns($tableName);

        if (!array_key_exists('VARCHAR_FIELD', $metaColumns)) {
            $this->testaddColumnToBasicTable();
        }

        $tableName = $this->testTableName;

        $flds = " 
            vArcHar_field C(50) NOTNULL DEFAULT ''
            ";

        $sqlArray = $this->dataDictionary->AddColumnSQL($tableName, $flds);

        $assertion = $this->assertIsArray(
            $sqlArray,
            'AddColumnSQL should return an array even ' .
            'if the column already exists with different case'
        );

        if ($assertion) {
            $this->assertCount(
                0,
                $sqlArray,
                'AddColumnSql should return an empty array ' .
                'if the column already exists'
            );
        }

        $flds = " 
            VARCHAR_FIELD C(50) NOTNULL DEFAULT ''
            ";

        $sqlArray = $this->dataDictionary->AddColumnSQL($tableName, $flds);

        $assertion = $this->assertIsArray(
            $sqlArray,
            'AddColumnSQL - should return an array even ' .
            'if the column already exists with same case'
        );

        if ($assertion) {
            $this->assertCount(
                0,
                $sqlArray,
                'AddColumnSql should return an empty array ' .
                'if the column already exists'
            );
        }
    }



}
