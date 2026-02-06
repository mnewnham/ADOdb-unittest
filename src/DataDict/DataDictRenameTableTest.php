<?php

/**
 * Tests cases for DataDictRenameTable functions of ADODb
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
 * Class DataDictRenameTableTest
 *
 * Test cases for for ADOdb DataDictRenameTable
 */
class DataDictRenameTableTest extends DataDictFunctions
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
     * Test for {@see ADODConnection::renameTableSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:renametable
     *
     * @return void
     */
    public function testRenameTable(): void
    {

        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not created successfully'
            );
            return;
        }

        if ($this->adoDriver == 'sqlite3') {
            $this->markTestSkipped(
                'Skipping test as rename table is not currently supported by SQLite'
            );
            return;
        }

        $sqlArray = $this->dataDictionary->renameTableSQL(
            'insertion_table',
            'insertion_table_renamed'
        );

        $assertionResult = $this->assertIsArray(
            $sqlArray,
            'Test of renameTableSQL - should return an array of SQL statements'
        );

        if (!$assertionResult) {
            $this->markTestSkipped(
                'Skipping test as renameTableSQL not supported by the driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        /*
        * Depends on the success of the metatables
        * function passing the new table name
        */
        $metaTables = $this->db->metaTables('T', '', 'insertion_table_renamed');

        $assertionResult = $this->assertFalse(
            $metaTables,
            'Test of renameTableSQL - new table insertion_table_renamed should exist'
        );

        if ($assertionResult) {
            $this->skipFollowingTests = true;
            return;
        }

        $this->assertSame(
            'insertion_table_renamed',
            $metaTables[0],
            'Test of renameTableSQL - renamed table exists'
        );

         $metaTables = $this->db->metaTables('T', '', 'insertion_table_renamed');


        if (empty($metaTables)) {
            $this->skipFollowingTests = true;
            return;
        }

        $sqlArray = $this->dataDictionary->renameTableSQL(
            'insertion_table_renamed',
            'insertion_table'
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }
    }

}
