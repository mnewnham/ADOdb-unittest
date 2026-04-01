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



       if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $this->db->startTrans();
        }

        $this->db->execute($sql);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $this->db->completeTrans();
        }

        $options = [
            'MYSQL' => "ENGINE MYISAM",
            'COMMENT' => 'TABLE COMMENT'
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
                 CLOB_FIELD X,
                 DECIMAL_FIELD N(8.4) DEFAULT 0 NOTNULL,
                 BOOLEAN_FIELD B NOTNULL DEFAULT 1,
                 DROPPABLE_FIELD N(10.6) DEFAULT 80.111,
              ";

        if ($GLOBALS['DriverControl']->hasNativeEnum) {
            $flds .= " ENUM_FIELD_TO_KEEP ENUM('duplo','lego','meccano')
            ";
        }
        $sqlArray = $this->dataDictionary->createTableSQL(
            'dictionary_creation_test_table',
            $flds,
            $options
        );

        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);

        $flipMetaTables = array_change_key_case(
            array_flip($this->db->metaTables()),
            CASE_UPPER
        );

        $this->assertArrayHasKey(
            'DICTIONARY_CREATION_TEST_TABLE',
            $flipMetaTables,
            'The dictionary Test Creation table should now be in the database'
        );

        $metaColumns = $this->db->metaColumns('dictionary_creation_test_table');

        if ($GLOBALS['DriverControl']->hasNativeEnum) {
            $columnCount = 14;
        } else {
            $columnCount = 13;
        }

        $this->assertEquals(
            $columnCount,
            count($metaColumns),
            'Newly created table should have ' . $columnCount . ' columns'
        );

        if (property_exists($this->dataDictionary, 'hasTableComments') && $this->dataDictionary->hasTableComments) {

            $sql =  $this->dataDictionary->getTableCommentSql(
                'dictionary_creation_test_table'
            );
            if ($sql !== null) { 
                
                $tableComment = $this->db->getOne($sql);
           
                $this->assertSame(
                    'TABLE COMMENT',
                    $tableComment,
                    'Table comment should have been assigned at creation'
                );
            }
        }
    }
}
