<?php

/**
 * Tests cases for DataDictCreateTable functions of ADODb
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
 * Class DataDictCreateTableTest
 *
 * Test cases for for ADOdb DataDictCreateTable
 */
class DataDictCreateTableTest extends DataDictFunctions
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
     * Test for {@see ADODConnection::CreateTableSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:createtablesql
     *
     * @return void
     */
    public function testCreateTableSql(): void
    {

        $sql = "DROP TABLE IF EXISTS dictionary_creation_test_table";

        list ($response,$errno,$errmsg) = $this->executeSqlString($sql, null, true);

        $flds = "ID I NOTNULL PRIMARY KEY AUTOINCREMENT,
                 DATE_FIELD D NOTNULL DEFAULT '2030-01-01',
                 VARCHAR_FIELD C(50) NOTNULL DEFAULT '',
                 INTEGER_FIELD I DEFAULT 0,
                 DECIMAL_FIELD_TO_MODIFY N(8.4) DEFAULT 0 NOTNULL,
                 BOOLEAN_FIELD_TO_RENAME I NOTNULL DEFAULT 0,
                 DROPPABLE_FIELD N(10.6) DEFAULT 80.111,
                 ENUM_FIELD_TO_KEEP ENUM('duplo','lego','meccano')
              ";
        $sqlArray = $this->dataDictionary->createTableSQL(
            'dictionary_creation_test_table',
            $flds
        );

        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);
    }
}
