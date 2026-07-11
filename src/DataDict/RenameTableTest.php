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
 * Class RenameTableTest
 *
 * Test cases for for ADOdb DataDictRenameTable
 */
class RenameTableTest extends DataDictFunctions
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
        
        $db = $GLOBALS['ADOdbConnection'];
       
        /*
        * Load the table to test data length tests
        */
        $schemaFile = sprintf(
            '%s/DatabaseSetup/rename-table-test.sql',
            $GLOBALS['unitTestToolsDirectory']
        );

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->startTrans();
        }

        $ok = readSqlIntoDatabase($db, $schemaFile);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->completeTrans();
        }

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

        $sql = 'DROP TABLE IF EXISTS rename_table_renamed';

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $this->db->startTrans();
        }

       $this->db->execute($sql);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $this->db->completeTrans();
        }

        $sqlArray = $this->dataDictionary->renameTableSQL(
            'rename_table',
            'rename_table_renamed'
        );

        if (!$sqlArray) {
            $this->markTestSkipped(
                'Skipping test as renameTableSQL not supported by the driver'
            );
            return;
        }

        $this->assertIsArray(
            $sqlArray,
            'Test of renameTableSQL - should return an array of SQL statements'
        );

        

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        /*
        * Depends on the success of the metatables
        * function passing the new table name
        */
        $success = $this->db->validateMetaTable('rename_table_renamed');
      
        $this->assertTrue(
            $success,
            'Test of renameTableSQL - new table rename_table_renamed should exist'
        );

        $sqlArray = $this->dataDictionary->renameTableSQL(
            'rename_table_renamed',
            'rename_table'
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $success = $this->db->validateMetaTable('rename_table');
      
        $this->assertTrue(
            $success,
            'Test of renameTableSQL - table rename_table_renamed' .
            ' should have been renamed back to rename_table'
        );
    }
}
