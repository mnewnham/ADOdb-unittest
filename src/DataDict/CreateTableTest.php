<?php

/**
 * Tests cases for DataDictCreateTable functions of ADODb
 *
 * This table creation stands alone. The resulting table is
 * not used for modification tests
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
 * Class CreateTableTest
 *
 * Test cases for for ADOdb DataDictCreateTable
 */
class CreateTableTest extends DataDictFunctions
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

        list ($response,$errno,$errmsg) = $this->executeSqlString($sql);


        $options = [
            'MYSQL' => 'ENGINE MYISAM;'
        ];

        $flds = "ID I NOTNULL PRIMARY KEY AUTOINCREMENT,
                 DATE_FIELD D NOTNULL DEFAULT '2030-01-01',
                 VARCHAR_FIELD C(50) NOTNULL DEFAULT '',
                 NVARCHAR_FIELD C2(50) NOTNULL DEFAULT '',
                 SMALLINT_FIELD I2 DEFAULT 0,
                 MEDIUMINT_FIELD I4 DEFAULT 0,
                 BIGINT_FIELD I8 DEFAULT 0,
                 INTEGER_FIELD I DEFAULT 0,
                 BLOB_FIELD B,
                 IMAGE_FIELD XL,
                 DECIMAL_FIELD N(8.4) DEFAULT 0 NOTNULL,
                 BOOLEAN_FIELD B NOTNULL DEFAULT 1,
                 DROPPABLE_FIELD N(10.6) DEFAULT 80.111,
                 ENUM_FIELD_TO_KEEP ENUM('duplo','lego','meccano')
              ";
        $sqlArray = $this->dataDictionary->createTableSQL(
            'dictionary_creation_test_table',
            $flds,
            $options
        );

        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);

        $flipMetaTables = array_flip($this->db->metaTables());

        $this->assertArrayHasKey(
            'dictionary_creation_test_table',
            $flipMetaTables,
            'The dictionary Test Creation table should now be in the database'
        );

        $metaColumns = $this->db->metaColumns('dictionary_creation_test_table');

        $this->assertEquals(
            14,
            count($metaColumns),
            'Newly created table should have 14 columns'
        );
    }
}
