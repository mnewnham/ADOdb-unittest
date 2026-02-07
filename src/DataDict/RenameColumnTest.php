<?php

/**
 * Tests cases for Dictionary Rename Column+ functions of ADODb
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
 * Class RenameColumnTest
 *
 * Test cases for for ADOdb DataDictRenameColumn
 */
class RenameColumnTest extends DataDictFunctions
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
     * Test for {@see ADODConnection::renameColumnSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:renamecolumnsql
     *
     * @return void
     */
    public function testRenameColumnInBasicTable(): void
    {

        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not created successfully'
            );
            return;
        }


        $sqlArray = $this->dataDictionary->renameColumnSQL(
            $this->testTableName,
            'BOOLEAN_FIELD_TO_RENAME',
            'ANOTHER_BOOLEAN_FIELD'
        );

        $assertion = $this->assertIsArray(
            $sqlArray,
            'renameColumnSql should return an array'
        );

        /*
        if ($assertion) {
            if (count($sqlArray) == 0) {
                $this->fail(
                    'renameColumnSql not supported by driver'
                );
                return;
            }
        }
        */

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayHasKey(
            'ANOTHER_BOOLEAN_FIELD',
            $metaColumns,
            'RenameColumnSQL should have renamed ' .
            'BOOLEAN_FIELD to ANOTHER_BOOLEAN_FIELD'
        );

        if (array_key_exists('ANOTHER_BOOLEAN_FIELD', $metaColumns)) {
            /*
            * reset the column name back to original
            */
            $sqlArray = $this->dataDictionary->renameColumnSQL(
                $this->testTableName,
                'ANOTHER_BOOLEAN_FIELD',
                'BOOLEAN_FIELD'
            );

            list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
            if ($errno > 0) {
                return;
            }

            $metaColumns = $this->db->metaColumnNames($this->testTableName);

            $this->assertArrayHasKey(
                'BOOLEAN_FIELD',
                $metaColumns,
                'RenameColumnSQL should have renamed ' .
                'ANOTHER_BOOLEAN_FIELD back to BOOLEAN_FIELD'
            );
        }
    }
}
