<?php

/**
 * Tests cases for Data Dictionary Drop Column functions of ADODb
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
 * Class DropColumnTest
 *
 * Test cases for for ADOdb DataDictDropColumn
 */
class DropColumnTest extends DataDictFunctions
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
     * Test for {@see ADODConnection::dropColumnSQL()}
     *
     * Written entirely by Copilot
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:dropcolumnsql
     *
     * @return void
     */
    public function testDropColumnInBasicTable(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not created successfully'
            );
            return;
        }


        $sqlArray = $this->dataDictionary->dropColumnSQL(
            $this->testTableName,
            'DROPPABLE_FIELD'
        );

        if (!is_array($sqlArray)) {
            $this->fail(
                'dropColumnSql() should always return an array'
            );
            return;
        }

        if (count($sqlArray) == 0) {
            $this->fail(
                'dropColumnSql() not supported by driver'
            );
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayNotHasKey(
            'DROPPABLE_FIELD',
            $metaColumns,
            'after executution of dropColumnSQL(), ' .
            'column DROPPABLE_FIELD should no longer exist'
        );

        if (array_key_exists('DROPPABLE_FIELD', $metaColumns)) {
            $this->skipFollowingTests = true;
        }
    }
}
