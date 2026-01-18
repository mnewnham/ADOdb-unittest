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
/**
 * Class ADOdbCustomDriver
 * Base Class for custom driver tests
 */

class ADOdbCustomDriver extends ADOdbTestCase
{
    protected ?object $xmlSchema;

    protected string $customMetaType = 'J';
    /**
     * The DB Physical identifier must be set in the
     * overload class
     *
     * @example MYSQLI_TYPE_JSON
     * @var     mixed $physicalType
     */
    protected mixed $physicalType;

    /**
     * The DB Field identifier must be set in the
     * overload class
     *
     * @example JSON
     * @var     mixed $columnType
     */
    protected ?string $columnType;

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

        $metaColumns = $this->db->metaColumns('mt_test');

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
            '%s/../DatabaseSetup/xmlschemafile-metatype.xml',
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
            '%s/../DatabaseSetup/xmlschemafile-metatype.xml',
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
}
