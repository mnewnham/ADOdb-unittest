<?php

/**
 * Tests cases for Dictionary Drop index functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\DataDict;

use MNewnham\ADOdbUnitTest\DataDict\DataDictFunctions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class DataDictDropIndexTest
 *
 * Test cases for for ADOdb DataDictDropIndexTest
 */
class DataDictDropIndexTest extends DataDictFunctions
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
     * Test for {@see ADODConnection::dropIndexSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:dropindexsql
     *
     * @return void
     */
    public function testdropIndexFromBasicTable(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped('Skipping tests as the table or column was not created successfully');
            return;
        }


        $sqlArray = $this->dataDictionary->dropIndexSQL(
            $this->testIndexName1,
            $this->testTableName
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaIndexes = $this->db->metaIndexes($this->testTableName);

        $this->assertArrayNotHasKey(
            $this->testIndexName1,
            $metaIndexes,
            'dropIndexSQL() Using Array For Fields ' .
            'should have dropped index ' . $this->testIndexName1
        );
    }

}
