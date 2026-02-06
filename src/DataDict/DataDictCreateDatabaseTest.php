<?php

/**
 * Tests cases for DataDictCreateDatabase functions of ADODb
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
 * Class DataDictCreateDatabaseTest
 *
 * Test cases for for ADOdb DataDictCreateDatabase
 */
class DataDictCreateDatabaseTest extends DataDictFunctions
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
     * Test for {@see ADODConnection::createDatabase()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:createdatabase
     *
     * @return void
     */
    public function testCreateDatabase(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not created successfully'
            );
            return;
        }

        /*
        * The default configuration for the tests is to skip database creation
        * Because this needs Create db privileges
        */
        if (!array_key_exists('meta', $GLOBALS['TestingControl'])) {
            $this->markTestSkipped(
                'Skipping database creation test as per configuration'
            );
            return;
        } elseif ($GLOBALS['TestingControl']['meta']['skipDbCreation']) {
            $this->markTestSkipped(
                'Skipping database creation test as per configuration'
            );
            return;
        }

        $dbName = 'unittest_database';
        $sqlArray = $this->dataDictionary->createDatabase($dbName);

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }


        // Check if the database was created successfully
        $metaDatabases = $this->db->metaDatabases();
        $this->assertContains(
            $dbName,
            $metaDatabases,
            'Test of createDatabase - database should exist'
        );

        // Clean up by dropping the database
        $this->dataDictionary->dropDatabase($dbName);
    }
}
