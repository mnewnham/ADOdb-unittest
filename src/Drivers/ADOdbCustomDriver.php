<?php

/**
 * Base Tests cases for custom drivers
 * Try to write database-agnostic tests where possible.
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

namespace MNewnham\ADOdbUnitTest\Drivers;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
/**
 * Class ADOdbCustomDriver
 * Base Class for custom driver tests
 */

class ADOdbCustomDriver extends ADOdbTestCase
{
    protected ?object $xmlSchema;
 
    /**
     * The custom metatype identifier
     *
     * @example 'J' for JSON
     * @var     string $customMetaType
     */
    protected string $customMetaType = 'J';
    
    /**
     * The DB Physical identifier must be set in the
     * overload class
     *
     * @example MYSQLI_TYPE_JSON
     * @var     mixed $physicalType
     */
    protected mixed $physicalType = null;

    /**
     * The DB Field identifier must be set in the
     * overload class
     *
     * @example JSON
     * @var     mixed $columnType
     */
    protected ?string $columnType;

    /**
     * The expected result from the qstr test which has
     * database-specific escaping. This is a reasonable default
     *
     * @var     string $qStrExpectedResult
     */
    protected string $qStrExpectedResult = '^(Famed author James O)[\\\'](\'Sullivan)$';

    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        if (!array_key_exists('xmlschema', $GLOBALS['TestingControl'])) {
            return;
        }

        $GLOBALS['ADOdbConnection']->startTrans();
        $GLOBALS['ADOdbConnection']->execute("DROP TABLE IF EXISTS testxmltable_1");
        $GLOBALS['ADOdbConnection']->completeTrans();
    }
    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();
    }

    /**
     * Tests adding a custom metatType to the available list
     *
     * @return void
     */
    public function testSetCustomMetaType(): void
    {
        
        if ($this->physicalType === null ||
            $this->columnType === null
        ) {
            $this->markTestSkipped(
                'Physical type and Column type must be set ' .
                'for the driver being tested'
            );
            return;
        }
    
        /*
        * We must define the custom type before loading the data dictionary
        */
        $ok = $this->db->setCustomMetaType(
            $this->customMetaType,
            $this->physicalType,
            $this->columnType
        );

        list($errno, $errmsg) = $this->assertADOdbError('setCustomMetaType()');

        $this->assertTrue(
            $ok,
            'setCustomMetaType() should successfully append a JSON metaType'
        );
    }

    /**
     * Test retrieving the list of custom metatypes
     *
     * @return void
     */
    public function testGetCustomMetaTypes(): void
    {

        $cmtArray = $this->db->getCustomMetaTypes();
        list($errno, $errmsg) = $this->assertADOdbError('getCustomMetaTypes()');

        if (!is_array($cmtArray)) {
            $this->fail('getCustomMetaTypes() shoud return an array');
            return;
        }

        $this->assertArrayHasKey(
            'J',
            $cmtArray,
            'J Custom metatype should be returned in list of custom metaTypes'
        );
    }

    /**
     * Tests creating a new table that includes a custom metatype
     *
     * @return void
     */
    public function testCreateTableWithCustomMetaType(): void
    {

        /*
        * Remove the table if it exists
        */
        $sql = "DROP TABLE IF EXISTS metatype_test";

        list ($response,$errno,$errmsg) = $this->executeSqlString($sql);

        /*
        * Create a new table with the standard syntax
        */
        $tabname = "metatype_test";
        $flds = " 
        COL1 I  NOTNULL AUTO PRIMARY,
        CUSTOM_JSON_COLUMN J
        ";

        $sqlArray = $this->dataDictionary->createTableSQL(
            'metatype_test',
            $flds
        );

        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);

        if ($errno > 0) {
            $this->fail(
                'Error creating table holding custom meta type'
            );
        }

        $metaColumns = $this->db->metaColumns('metatype_test');

        $this->assertArrayHasKey(
            'CUSTOM_JSON_COLUMN',
            $metaColumns,
            'createTableSQL() should have added CUSTOM_JSON_COLUMN'
        );
    }

    /**
     * Tests changing a table, adding a new column with a custom metatype
     *
     * @return void
     */
    public function testChangeTableWithCustomMetaType(): void
    {

        $tabname = "metatype_test";
        $flds = " 
        ADDITIONAL_JSON_COLUMN J
        ";

        $sqlArray = $this->dataDictionary->changeTableSQL(
            'metatype_test',
            $flds
        );

        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);

        if ($errno > 0) {
            $this->fail(
                'Error adding additional custom meta type'
            );
        }

        $metaColumns = $this->db->metaColumns('metatype_test');


        $this->assertArrayHasKey(
            'ADDITIONAL_JSON_COLUMN',
            $metaColumns,
            'createTableSQL() should have added ADDITIONAL_JSON_COLUMN'
        );
    }

    /**
     * Tests using XMLSchema to add a new table with a Custom MetaType (J=JSON)
     *
     * @return void
     */
    public function testCreateCustomFieldWithXmlSchema(): void
    {


        if (!array_key_exists('xmlschema', $GLOBALS['TestingControl'])) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped('ADOxmlSchema testing is disabled');
            return;
        }
        if ($GLOBALS['TestingControl']['xmlschema']['skipXmlTests'] == 1) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped('ADOxmlSchema testing is disabled');
            return;
        }


        $this->xmlSchema = $GLOBALS['ADOxmlSchema'] ;
        /*
        * Load the file designed to create then modify
        * a table containing custom metaTypes
        * using the XMLSchema functions
        */
        $schemaFile = sprintf(
            '%s/../tools/DatabaseSetup/xmlschemafile-metatype.xml',
            dirname(__FILE__)
        );


        $ok = $this->xmlSchema->parseSchema($schemaFile);

        if (!$ok) {
            $this->assertTrue(
                $ok,
                'XML Custom MetaType Schema Creation File parsing failed'
            );
            $this->markTestSkipped('XML Schema Creation parsing failed');
            $this->skipFollowingTests = true;
            return;
        }


        $ok = $this->xmlSchema->executeSchema();
        list($errno, $errmsg) = $this->assertADOdbError('xml->executeSchema()');

        $this->assertSame(
            2, // Successful operations
            $ok,
            'XML Schema Creation failed'
        );
        if ($ok !== 2) {
            $this->markTestSkipped(
                'Schema File Creation failed, ' .
                'skipping XML Schema tests'
            );
            return;
        }

        $table = 'testxmltable_1';
        $fields = $this->db->MetaColumns($table);

        $this->assertNotEmpty(
            $fields,
            'No fields found in the table'
        );

        $this->assertArrayHasKey(
            'ID',
            $fields,
            'Field "id" not found in the table'
        );

        $this->assertArrayHasKey(
            'JSON_FIELD_TO_ADD',
            $fields,
            'Field "json_field_to_add" not found in the table. Customer MetaType Creation has failed'
        );
    }

    /**
     * Tests using XMLSchema to update a table,
     * adding a field with a custom MetaType (J=JSON)
     *
     * @return void
     */
    public function testUpdateCustomFieldWithXmlSchema(): void
    {


        if (!array_key_exists('xmlschema', $GLOBALS['TestingControl'])) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped('ADOxmlSchema testing is disabled');
            return;
        }
        if ($GLOBALS['TestingControl']['xmlschema']['skipXmlTests'] == 1) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped('ADOxmlSchema testing is disabled');
            return;
        }


        /*
        * Drop the JSON column so that we can re-add it again
        */
        $sqlArray = $this->dataDictionary->dropColumnSql(
            'testxmltable_1',
            'json_field_to_add'
        );

         list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);

        if ($errno > 0) {
            $this->fail(
                'Error dropping custom meta type field'
            );
        }


        $this->xmlSchema = $GLOBALS['ADOxmlSchema'] ;
        /*
        * ReLoad the custom metatype XML files designed to create then modify
        * a table using the XMLSchema functions
        */
        $schemaFile = sprintf(
            '%s/../tools/DatabaseSetup/xmlschemafile-metatype.xml',
            dirname(__FILE__)
        );


        $ok = $this->xmlSchema->parseSchema($schemaFile);

        if (!$ok) {
            $this->assertTrue(
                $ok,
                'XML Schema Updating File parsing failed'
            );
            $this->markTestSkipped('XML Schema Update parsing failed');
            $this->skipFollowingTests = true;
            return;
        }


        $ok = $this->xmlSchema->executeSchema();
        list($errno, $errmsg) = $this->assertADOdbError('xml->executeSchema()');

        $this->assertSame(
            2, // Successful operations
            $ok,
            'XML Schema Update failed'
        );
        if ($ok !== 2) {
            $this->markTestSkipped(
                'Schema File Update failed, ' .
                'skipping XML Schema tests'
            );
            return;
        }

        $table = 'testxmltable_1';
        $fields = $this->db->MetaColumns($table);

        $this->assertNotEmpty(
            $fields,
            'No fields found in the table'
        );

        $this->assertArrayHasKey(
            'ID',
            $fields,
            'Field "id" not found in the table after update'
        );

        $this->assertArrayHasKey(
            'JSON_FIELD_TO_ADD',
            $fields,
            'Field "json_field_to_add" not found in the table. ' .
            'Custom MetaType Update has failed'
        );
    }
    
    /**
     * Test for {@see ADODConnection::qstr()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:qstr
     *
     * @return void
     */
    public function testQstr(): void
    {
        /*
        * The expected result is db dependent, so we will
        * inser the string into the empty_field column
        * and see if it fails to insert or not.
        */
        $testString = "Famed author James O'Sullivan";

        /*
        * Blank out the empty_field column first to ensure that
        * the total number of rows updated is correct
        */
        $SQL = "UPDATE testtable_3 SET empty_field = null";

        list($result, $errno, $errmsg) = $this->executeSqlString($SQL);

        if ($errno > 0) {
            return;
        }

        $SQL = "UPDATE testtable_3 SET empty_field = {$this->db->qstr($testString)}";

        list($result, $errno, $errmsg) = $this->executeSqlString($SQL);

        if ($errno > 0) {
            return;
        }

        $expectedValue = 11;
        $actualValue = $this->db->Affected_Rows();

        list($errno, $errmsg) = $this->assertADOdbError('Affected_Rows()');


        // We should have updated 11 rows
        $this->assertSame(
            $expectedValue,
            $actualValue,
            'All rows should have been updated with the test string'
        );

        // Now we will check the value in the empty_field column
        $sql = "SELECT empty_field FROM testtable_3";

        $this->db->startTrans();
        $returnValue = $this->db->getOne($sql);

        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->db->CompleteTrans();
        if ($errno > 0) {
            return;
        }

       
        $qStrResult = sprintf('/%s/', $this->qStrExpectedResult);

        $testResult = preg_match($qStrResult, $returnValue);

        $this->assertSame(
            true,
            $testResult,
            'Qstr should have returned a string with the apostrophe escaped via the database-specific method'
        );
    }

    /**
     * Test for {@see ADODConnection::addq()}
     *
     * @link   https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:addq
     * @return void
     */
    public function testAddq(): void
    {

        /*
        * The expected result is db dependent, so we will
        * insert the string into the empty_field column
        * and see if it fails to insert or not.
        */
        $testString = "Famed author James O'Sullivan";
        $p1 = $this->db->param('p1');
        $bind = array(
            'p1' => $this->db->addQ($testString)
        );

        $sql = "UPDATE testtable_3 SET empty_field = $p1";

        list($result, $errno, $errmsg) = $this->executeSqlString($sql, $bind);

        if ($errno > 0) {
            return;
        }

        $affectedRows =  $this->db->Affected_Rows();

        list($errno, $errmsg) = $this->assertADOdbError('Affected_Rows()');


        // We should have updated 11 rows
        $this->assertSame(
            11,
            $affectedRows,
            'All rows should have been updated with the test string'
        );

        // Now we will check the value in the empty_field column
        $sql = "SELECT empty_field FROM testtable_1";

        $returnValue = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        // Now we will check the value in the empty_field column
        $sql = "SELECT empty_field FROM testtable_3";

        $returnValue = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $testResult = preg_match('/^(Famed author James O)[\\\'](\'Sullivan)$/', $returnValue);

        $this->assertSame(
            true,
            $testResult,
            'addQ should have returned a string with the apostrophe escaped'
        );
    }

    /**
     * Test for {@see ADODConnection::concat()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:concat
     *
     * @return void
     */
    #[DataProvider('providerTestConcat')]
    public function testConcat(int $fetchMode, string $firstColumn, string $secondColumn): void
    {

        /*
        * Find a record that has a varchar_field value
        */

        $this->db->setFetchMode($fetchMode);

        $sql = "SELECT number_run_field, varchar_field 
                  FROM testtable_1 
                 WHERE varchar_field IS NOT NULL";


        $row = $this->db->getRow($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $expectedValue = sprintf(
            '%s|%s',
            $row[$secondColumn],
            $row[$secondColumn]
        );

        $field = $this->db->Concat('varchar_field', "'|'", 'varchar_field');
        list($errno, $errmsg) = $this->assertADOdbError('concat()');

        $sql = "SELECT $field 
                  FROM testtable_1 
                 WHERE number_run_field={$row[$firstColumn]}";


        $result = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $expectedValue,
            $result,
            sprintf('3 value concat should return %s', $expectedValue)
        );

        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }

    /**
     * Data provider for {@see testConcat()}
     *
     * @return array [int $fetchmode, string $number_run_column, string $varchar_column]
     */
    static function providerTestConcat(): array
    {

        switch (ADODB_ASSOC_CASE) {
            case ADODB_ASSOC_CASE_UPPER:
                return [
                'FETCH_ASSOC,ASSOC_CASE_UPPER' =>
                array(
                    ADODB_FETCH_ASSOC,
                    'NUMBER_RUN_FIELD',
                    'VARCHAR_FIELD',
                ),
                'FETCH_NUM,ASSOC_CASE_UPPER' =>
                array(
                    0 => ADODB_FETCH_NUM,
                    1 => "0",
                    2 => "1"

                )
            ];
            break;

            case ADODB_ASSOC_CASE_LOWER:
            default:
                return [
                'FETCH_ASSOC,ASSOC_CASE_LOWER' => [
                    ADODB_FETCH_ASSOC,
                    'number_run_field',
                    'varchar_field',
                ],
                'FETCH_NUM,ASSOC_CASE_UPPER' => [
                    0 => ADODB_FETCH_NUM,
                    1 => "0",
                    2 => "1"
                ]
            ];

            break;
        }
    }

    /**
     * Test for {@see ADODConnection::ifNull()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:ifnull
     *
     * @return void
     */
    #[DataProvider('providerTestIfnull')]
    public function testIfNull(int $fetchMode, string $firstColumn, string $secondColumn): void
    {



        $this->db->setFetchMode($fetchMode);

        $sql = "SELECT number_run_field, decimal_field 
                  FROM testtable_1 
                 WHERE date_field IS NOT NULL";

        $row = $this->db->getRow($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        /*
        * Set up a test record that has a NULL value
        */
        $sql = "UPDATE testtable_1 
                   SET decimal_field = null 
                 WHERE number_run_field={$row[$firstColumn]}";

        list($result, $errno, $errmsg) = $this->executeSqlString($sql);
        if ($errno > 0) {
            return;
        }

        /*
        * Now get a weird value back from the ifnull function
        */

        $sql = "SELECT {$this->db->ifNull('decimal_field', 8675304)} 
                  FROM testtable_1 
                 WHERE number_run_field={$row[$firstColumn]}";

        $expectedResult = (float)$this->db->getOne($sql);

        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            8675304,
            $expectedResult,
            'Test of ifnull function  should return 8675304'
        );

        /*
        * Reset the date_field to a non-null value
        */
        $sql = "UPDATE testtable_1 
                   SET decimal_field = {$row[$secondColumn]} 
                 WHERE number_run_field={$row[$firstColumn]}";

        list($result, $errno, $errmsg) = $this->executeSqlString($sql);

        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }

    /**
     * Data provider for {@see testIfnull()}
     *
     * @return array [int fetchmode, string number_run column, string date column]
     */
    static function providerTestIfnull(): array
    {


        switch (ADODB_ASSOC_CASE) {
            case ADODB_ASSOC_CASE_UPPER:
                return [
                'FETCH_ASSOC,ASSOC_CASE_UPPER' => [
                    ADODB_FETCH_ASSOC,
                    'NUMBER_RUN_FIELD',
                    'DECIMAL_FIELD',
                ],
                'FETCH_NUM,ASSOC_CASE_UPPER' => [
                    0 => ADODB_FETCH_NUM,
                    1 => "0",
                    2 => "1"

                ]
            ];
            break;
            case ADODB_ASSOC_CASE_LOWER:
            default:
                return [
                'FETCH_ASSOC,ASSOC_CASE_LOWER' => [
                    ADODB_FETCH_ASSOC,
                    'number_run_field',
                    'decimal_field',
                ],
                'FETCH_NUM,ASSOC_CASE_UPPER' => [
                    0 => ADODB_FETCH_NUM,
                    1 => "0",
                    2 => "1"

                ]
            ];
            break;
        }
    }
}
