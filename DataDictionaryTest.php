<?php

/**
 * Tests cases for data dictionary functions of ADODb, such as table,
 * column and index creation
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
 * @link https://github.com/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

use PHPUnit\Framework\TestCase;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class DataDictionaryTest extends ADOdbTestCase
{
    protected bool $skipCommentTests = false;

    protected string $testTableName = 'insertion_table';
    protected string $testIndexName1 = 'insertion_index_1';
    protected string $testIndexName2 = 'insertion_index_2';


    /**
     * Sets up a flag used from refreshing the table mid-test
     *
     * @return void
     */
    public static function setupBeforeClass(): void
    {
        $GLOBALS['baseTestsComplete'] = 0;
    }

    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();
        /*
        * Find the correct test table name
        */

        //$this->buildBasicTable();
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

        $sql = "DROP TABLE IF EXISTS {$this->testTableName}";

        list ($response,$errno,$errmsg) = $this->executeSqlString($sql);

        $flds = "ID I NOTNULL PRIMARY KEY AUTOINCREMENT,
                 DATE_FIELD D NOTNULL DEFAULT '2010-01-01',
                 VARCHAR_FIELD C(50) NOTNULL DEFAULT '',
                 INTEGER_FIELD I DEFAULT 0,
                 ENUM_FIELD_TO_KEEP ENUM('duplo','lego','meccano')
              ";
        $sqlArray = $this->dataDictionary->createTableSQL(
            $this->testTableName,
            $flds
        );

        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);
    }


    public function testChangeTableSql(): void
    {


        $flds = "ID I NOTNULL PRIMARY KEY AUTOINCREMENT,
                VARCHAR_FIELD C(50) NOTNULL DEFAULT 'This is a default value with spaces',
                DATE_FIELD D NOTNULL DEFAULT '2010-01-01',
                DATE_FIELD_WITH_DEFDATE D NOTNULL DEFDATE,
                TIMESTAMP_FIELD_WITH_DEFDATE TS NOTNULL DEFTIMESTAMP,
                INTEGER_FIELD I4 NOTNULL DEFAULT 0,
                UNSIGNED_INTEGER_FIELD I4 UNSIGNED NOTNULL DEFAULT 0,
                BOOLEAN_FIELD I NOTNULL DEFAULT 0,
                DECIMAL_FIELD N(8.4) DEFAULT 0 NOTNULL,
                DROPPABLE_FIELD N(10.6) DEFAULT 80.111,
                BLOB_FIELD B,
                LONG_FIELD XL,
                ENUM_FIELD ENUM('lions','tigers','halibut') DEFAULT 'tigers'
        ";

        /*
         $flds = "ID I NOTNULL PRIMARY KEY AUTOINCREMENT,
                VARCHAR_FIELD C(50) NOTNULL,
                DATE_FIELD D NOTNULL DEFAULT '2010-01-01',
                DATE_FIELD_WITH_DEFDATE D NOTNULL DEFDATE,
                TIMESTAMP_FIELD_WITH_DEFDATE TS NOTNULL DEFTIMESTAMP,
                INTEGER_FIELD I4 NOTNULL DEFAULT 0,
                UNSIGNED_INTEGER_FIELD I4 UNSIGNED NOTNULL DEFAULT 0,
                BOOLEAN_FIELD I NOTNULL DEFAULT 0,
                DECIMAL_FIELD N(8.4) DEFAULT 0 NOTNULL,
                DROPPABLE_FIELD N(10.6) DEFAULT 80.111,
                BLOB_FIELD B,
                LONG_FIELD XL
              
        ";
        */

        $sqlArray = $this->dataDictionary->changeTableSQL(
            $this->testTableName,
            $flds
        );


        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);

        if ($errno > 0) {
            $this->fail(
                'Error creating insertion_table'
            );
        }
    }

    /**
     * Test for {@see ADODConnection::addColumnSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:addcolumnsql
     *
     * @return void
     */
    public function testaddColumnToBasicTable(): void
    {


        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not created successfully'
            );
            return;
        }

        $flds = " 
            NEW_VARCHAR_FIELD C(50) NOTNULL DEFAULT '',
            NEW_DATE_FIELD D NOTNULL DEFAULT '2010-01-01',
            NEW_INTEGER_FIELD I4 NOTNULL DEFAULT 0,
            NEW_BOOLEAN_FIELD L NOTNULL DEFAULT 0,
            NEW_DECIMAL_FIELD N(8.4) DEFAULT 0,
            NEW_DROPPABLE_FIELD N(10.6) DEFAULT 80.111
            ";

        $sqlArray = $this->dataDictionary->AddColumnSQL($this->testTableName, $flds);

        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);

        $GLOBALS['baseTestsComplete'] = true;


        if ($errno > 0) {
            if ($this->baseTestsComplete == false) {
                $this->skipFollowingTests(
                    'Base table buildout failed'
                );
            }
            return;
        }



        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayHasKey(
            'VARCHAR_FIELD',
            $metaColumns,
            'Test of AddColumnSQL'
        );

        if (!array_key_exists('VARCHAR_FIELD', $metaColumns)) {
            $this->skipFollowingTests = true;
        }
    }

    /**
     * Test for {@see ADODConnection::alterColumnSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:altercolumnsql
     *
     * @return void
     */
    public function testalterColumnInBasicTable(): void
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
            $metaColumns = $this->db->metaColumns($tableName);
        }

        $flds = " 
            VARCHAR_FIELD VARCHAR(120) NOTNULL DEFAULT ''
            ";
         $flds = " 
            VARCHAR_FIELD VARCHAR(120)
            ";

        $sqlArray = $this->dataDictionary->alterColumnSQL(
            $tableName,
            $flds
        );

        if (count($sqlArray) == 0) {
            $this->fail(
                'AlterColumnSql() not supported currently by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        /*
        * re-read the column definitions
        */
        $metaColumns = $this->db->metaColumns($tableName);

        $this->assertArrayHasKey(
            'VARCHAR_FIELD',
            $metaColumns,
            'AlterColumnSQL should not remove the VARCHAR_FIELD from the table'
        );

        $this->assertSame(
            '120',
            (string)$metaColumns['VARCHAR_FIELD']->max_length,
            'AlterColumnSQL should have Increased the ' .
            'length of VARCHAR_FIELD to from 50 to 120'
        );

        $flds = " 
            INTEGER_FIELD I8 NOTNULL DEFAULT 1
            ";

        $sqlArray = $this->dataDictionary->alterColumnSQL(
            $tableName,
            $flds
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }
        /*
        * re-read the column definitions
        */
        $metaColumns = $this->db->metaColumns($tableName);

        $this->assertArrayHasKey(
            'INTEGER_FIELD',
            $metaColumns,
            'AltercolumnSQL INTEGER_FIELD should still exist in the table'
        );

        $this->assertSame(
            '1',
            $metaColumns['INTEGER_FIELD']->default_value,
            'AltercolumnSql should have change the default ' .
            'of INTEGER_FIELD from 0 to 1'
        );

        /*
        * Change the scale of the decimal field
        */

         $flds = " 
            DECIMAL_FIELD N(16.12) NOTNULL
            ";

        $sqlArray = $this->dataDictionary->alterColumnSQL(
            $tableName,
            $flds
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        /*
        * re-read the column definitions
        */
        $metaColumns = $this->db->metaColumns($tableName);

        $this->assertArrayHasKey(
            'DECIMAL_FIELD',
            $metaColumns,
            'AltercolumnSQL DECIMAL_FIELD should still exist in the table'
        );

        $this->assertSame(
            '16',
            $metaColumns['DECIMAL_FIELD']->max_length,
            'AlterColumnSQL: maxlength of DECIMAL_FIELD' .
            'should have changed from 8 to 16'
        );

        $this->assertSame(
            '12',
            $metaColumns['DECIMAL_FIELD']->scale,
            'AlterColumnSQL: Change of scale of DECIMAL_FIELD 4 to 12'
        );
    }

    /**
     * Test for {@see ADODConnection::addColumnSQL()} adding a duplicate column with different case
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:addcolumnsql
     *
     * @return void
     */
    function testAddDuplicateCasedColumn(): void
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
            'BOOLEAN_FIELD',
            'ANOTHER_BOOLEAN_FIELD'
        );

        $assertion = $this->assertIsArray(
            $sqlArray,
            'renameColumnSql should return an array'
        );

        if ($assertion) {
            if (count($sqlArray) == 0) {
                $this->fail(
                    'renameColumnSql not supported by driver'
                );
                return;
            }
        }

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

    /**
     * Test for {@see ADODConnection::createIndexSQL()} passing a string
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:createindexsql
     *
     * @return void
     */
    public function testaddIndexToBasicTableViaString(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table or column ' .
                'was not created successfully'
            );
            return;
        }


        $sql = "DROP TABLE IF EXISTS {$this->testIndexName1}";

        list ($response,$errno,$errmsg) = $this->executeSqlString($sql);

        $flds = "VARCHAR_FIELD, DATE_FIELD, INTEGER_FIELD";
        $indexOptions = array(
            'UNIQUE'
        );

        $sqlArray = $this->dataDictionary->createIndexSQL(
            $this->testIndexName1,
            $this->testTableName,
            $flds,
            $indexOptions
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaIndexes = $this->db->metaIndexes($this->testTableName);

        $this->assertArrayHasKey(
            $this->testIndexName1,
            $metaIndexes,
            'AddIndexSQL Using String For Fields should now ' .
            'contain index ' . $this->testIndexName1
        );
    }

    /**
     * Test for {@see ADODConnection::createIndexSQL()} passing an array
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:createindexsql
     *
     * @return void
     */
    public function testaddIndexToBasicTableViaArray(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table or ' .
                'column was not created successfully'
            );
            return;
        }

        $flds = array(
            "DATE_FIELD",
            "INTEGER_FIELD",
            "VARCHAR_FIELD"
        );
        $indexOptions = array(
            'UNIQUE'
        );

        $sqlArray = $this->dataDictionary->createIndexSQL(
            $this->testIndexName2,
            $this->testTableName,
            $flds,
            $indexOptions
        );

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $GLOBALS['baseTestsComplete'] = 2;

        $metaIndexes = $this->db->metaIndexes($this->testTableName);

        $this->assertArrayHasKey(
            $this->testIndexName2,
            $metaIndexes,
            'AddIndexSQL Using Array For Fields should have ' .
            'added index ' . $this->testIndexName1
        );
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

    /**
     * Test for {@see ADODConnection::changeTableSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:changetablesql
     *
     * @return void
     */
    public function testChangeTable(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not ' .
                'created successfully'
            );
            return;
        }

        $flds = " 
            VARCHAR_FIELD VARCHAR(50) NOTNULL DEFAULT '',
            DATE_FIELD DATE NOTNULL DEFAULT '2010-01-01',
            ANOTHER_INTEGER_FIELD INTEGER NOTNULL DEFAULT 0,
            YET_ANOTHER_VARCHAR_FIELD VARCHAR(50) NOTNULL DEFAULT ''
            ";

        $sqlArray = $this->dataDictionary->changeTableSQL(
            $this->testTableName,
            $flds
        );

        $assertion = $this->assertIsArray(
            $sqlArray,
            'changeTableSql() should alway return an array'
        );

        if (!$assertion) {
            return;
        }

        if (count($sqlArray) == 0) {
            $this->fail(
                'changeTableSql() not supported by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }


        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayHasKey(
            'INTEGER_FIELD',
            $metaColumns,
            'changeTableSQL() using $dropflds=false ' .
            '- old column should be retained even if ' .
            'not in the new definition'
        );

        $this->assertArrayHasKey(
            'ANOTHER_INTEGER_FIELD',
            $metaColumns,
            'changeTableSql() ANOTHER_INTEGER_FIELD should have been added'
        );


        $this->assertArrayHasKey(
            'YET_ANOTHER_VARCHAR_FIELD',
            $metaColumns,
            'changeTableSQ() YET_ANOTHER_VARCHAR_FIELD should have been added'
        );

        if (!array_key_exists('ANOTHER_VARCHAR_FIELD', $metaColumns)) {
            $this->skipFollowingTests = true;
        }

        /*
        * Now re-execute wth the drop flag set to true
        */
        $sqlArray = $this->dataDictionary->changeTableSQL(
            $this->testTableName,
            $flds,
            false,
            true
        );


        $assertion = $this->assertIsArray(
            $sqlArray,
            'changeTableSql() should alway return an array'
        );

        if (!$assertion) {
            return;
        }

        if (count($sqlArray) == 0) {
            $this->fail(
                'changeTableSql() not supported by driver'
            );
            return;
        }

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayNotHasKey(
            'INTEGER_FIELD',
            $metaColumns,
            'changeTableSQL() using $dropFlds=true ' .
            'old column INTEGER_FIELD should be dropped'
        );
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

    /**
     * Test for {@see ADODConnection::dropTableSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:droptablesql
     *
     * @return void
     */
    public function testDropTable(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping tests as the table was not created successfully'
            );
            return;
        }


        $sqlArray = $this->dataDictionary->dropTableSQL($this->testTableName);

        list($result, $errno, $errmsg) = $this->executeDictionaryAction($sqlArray);
        if ($errno > 0) {
            return;
        }

        $metaTables = $this->db->metaTables('T', '', $this->testTableName);

        $this->assertArrayNotHasKey(
            $this->testTableName,
            $metaTables,
            'Test of dropTableSQL - table should not exist'
        );
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
