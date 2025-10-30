<?php

/**
 * Tests cases for XMLSchema functions of ADODb
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
 * Class XMLSchemaTest
 *
 * Test cases for for ADOdb XMLSchema functions
 */
class XmlSchemaTest extends ADOdbTestCase
{
    protected ?object $xmlSchema;

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
    }


    /**
     * Test the XML Schema creation
     *
     * @return void
     */
    public function testXmlSchemaCreation(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped('Skipping XML Schema tests');
            return;
        }

        /*
        * Load the first of 2 files designed to create then modify
        * a table using the XMLSchema functions
        */
        $schemaFile = sprintf('%s/DatabaseSetup/xmlschemafile-create.xml', dirname(__FILE__));


        $ok = $this->xmlSchema->parseSchema($schemaFile);

        if (!$ok) {
            $this->assertTrue(
                $ok,
                'XML Schema Creation File parsing failed'
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
            $this->markTestSkipped('Schema File Creation failed, skipping XML Schema tests');
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
            'VARCHAR_FIELD',
            $fields,
            'Field "varchar_fields" not found in the table'
        );

        $this->assertArrayHasKey(
            'INTEGER_FIELD',
            $fields,
            'Field "integer_fields" not found in the table'
        );

        $this->assertArrayHasKey(
            'DECIMAL_FIELD',
            $fields,
            'Field "decimal_fields" not found in the table'
        );
    }

    /**
     * Applies a test to update a schena using XML
     *
     * @return void
     */
    public function testXmlSchemaUpdate(): void
    {

        /**
         * Load the second file to test the XML Schema update
         */
        $schemaFile = sprintf('%s/DatabaseSetup/xmlschemafile-update.xml', dirname(__FILE__));
        $this->assertFileExists(
            $schemaFile,
            'Schema file does not exist: ' . $schemaFile
        );


        print "
###################################################
START PARSING UPDATE XML SCHEMA
###################################################
";
        

        $ok = $this->xmlSchema->parseSchema($schemaFile);
        list($errno, $errmsg) = $this->assertADOdbError('xml->parseSchema()');

        if (!$ok) {
            $this->assertTrue(
                $ok,
                'XML Schema parsing for table update failed'
            );
            $this->markTestSkipped('XML Schema parsing failed updating table');
            $this->skipFollowingTests = true;
            return;
        }

        $ok = $this->xmlSchema->executeSchema();
        list($errno, $errmsg) = $this->assertADOdbError('xml->executeSchema()');

        $this->assertSame(
            2,
            $ok,
            'XML Schema update failed after calling executeSchema()'
        );

        /**
        * Test the update fields in the table
        */

        $table = 'testxmltable_1';
        $fields = $this->db->MetaColumns($table);


        $this->assertArrayNotHasKey(
            'VARCHAR_FIELD_TO_DROP',
            $fields,
            'Field "varchar_field_to_drop" should not be found in the table'
        );


        $this->assertArrayHasKey(
            'VARCHAR_FIELD_TO_ADD',
            $fields,
            'Field "varchar_field_to_add" should now be found in the table'
        );
    }

    /**
     * Tests dropping the Schema using XML
     *
     * @return void
     */
    public function testXmlSchemaDrop(): void
    {

        /**
         * Load the third file to drop the XML Schema update
         */
        $schemaFile = sprintf('%s/DatabaseSetup/xmlschemafile-drop.xml', dirname(__FILE__));
        $this->assertFileExists(
            $schemaFile,
            'Schema file does not exist: ' . $schemaFile
        );

         print "
###################################################
START PARSING DROP XML SCHEMA
###################################################
";

        $ok = $this->xmlSchema->parseSchema($schemaFile);
        list($errno, $errmsg) = $this->assertADOdbError('xml->parseSchema()');

        if (!$ok) {
            $this->assertTrue(
                $ok,
                'XML Schema parsing for table drop failed'
            );
            $this->markTestSkipped('XML Schema parsing failed for drop table');
            $this->skipFollowingTests = true;
            return;
        }

        $ok = $this->xmlSchema->executeSchema();
        list($errno, $errmsg) = $this->assertADOdbError('xml->executeSchema()');

        $this->assertSame(
            2,
            $ok,
            'XML Schema drop failed after calling executeSchema()'
        );

        /**
        * Test the table does not exist
        */

        $tables = $this->db->metaTables();

        $this->assertNotContains(
            'TESTXMLTABLE_1',
            $tables,
            'table testxmltable_1 should not be found in the database'
        );
    }
}
