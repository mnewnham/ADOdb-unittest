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
 * @copyright 2025,2026 Mark Newnham
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 *
 * @link https://github.com/mnewnham/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

namespace MNewnham\ADOdbUnitTest\XmlSchema;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class XMLSchemaTest
 *
 * Test cases for for ADOdb XMLSchema functions
 */
class XmlSchemaTest extends ADOdbTestCase
{
   /**
    * Holding point for the XMLSchema object
    * @var object|null
    */
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

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $GLOBALS['ADOdbConnection']->startTrans();
        }

        $GLOBALS['ADOdbConnection']->execute("DROP TABLE IF EXISTS xml_schema_test");
        $GLOBALS['ADOdbConnection']->execute("DROP TABLE IF EXISTS XML_SCHEMA_TEST");

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $GLOBALS['ADOdbConnection']->completeTrans();
        }
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
        $schemaFile = sprintf(
            '%s/DatabaseSetup/xmlschemafile-create.xml',
            $GLOBALS['unitTestToolsDirectory']
        );

        if (!file_exists($schemaFile)) {
            die('NO FILE FOR File ' . $schemaFile);
        }

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

        $table = 'xml_schema_test';
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

        if (property_exists($this->dataDictionary, 'hasTableComments') && $this->dataDictionary->hasTableComments) {
            $sql =  $this->dataDictionary->getTableCommentSql(
                'xml_schema_test'
            );
            if ($sql !== null) {
                $tableComment = $this->db->getOne($sql);

                $this->assertSame(
                    'XML SCHEMA COMMENT',
                    $tableComment,
                    'Table comment should have been assigned at XML schema creation'
                );
            }
        }


        if (property_exists($this->dataDictionary, 'hasColumnComments') && $this->dataDictionary->hasColumnComments) {
            $sql =  $this->dataDictionary->getColumnCommentSql(
                'xml_schema_test',
                'date_field_to_keep'
            );
            if ($sql !== null) {
                $columnComment = $this->db->getOne($sql);

                print "
                CC $sql | $columnComment
                ";

                $this->assertSame(
                    'DATE FIELD COMMENT',
                    $columnComment,
                    'Column comment should have been assigned at creation'
                );
            }
        }

        if (property_exists($this->dataDictionary, 'hasIndexComments') && $this->dataDictionary->hasIndexComments) {
            $sql =  $this->dataDictionary->getIndexCommentSql(
                'xml_schema_test',
                'droppable_index'
            );
            if ($sql !== null) {
                $indexComment = $this->db->getOne($sql);

                $this->assertSame(
                    'DROPPABLE INDEX COMMENT',
                    $indexComment,
                    'Index comment should have been assigned at creation'
                );
            }
        } else {
            $this->markTestIncomplete(
                'No index comment support for driver'
            );
        }
    }

    /**
     * Applies a test to update a schena using XML
     *
     * @return void
     */
    public function testXmlSchemaUpdate(): void
    {


        $schemaFile = sprintf(
            '%s/DatabaseSetup/xmlschemafile-update.xml',
            $GLOBALS['unitTestToolsDirectory']
        );

        $this->assertFileExists(
            $schemaFile,
            'Schema file does not exist: ' . $schemaFile
        );

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
        $table = 'xml_schema_test';
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

        if (property_exists($this->dataDictionary, 'hasColumnComments') && $this->dataDictionary->hasColumnComments) {
            $sql =  $this->dataDictionary->getColumnCommentSql(
                'xml_schema_test',
                'date_field_to_keep'
            );
            if ($sql !== null) {
                $columnComment = $this->db->getOne($sql);

                $this->assertSame(
                    'MODIFIED DATE FIELD COMMENT',
                    $columnComment,
                    'Column comment should have been assigned at creation'
                );
            }
        }
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
        $schemaFile = sprintf(
            '%s//DatabaseSetup/xmlschemafile-drop.xml',
            $GLOBALS['unitTestToolsDirectory']
        );
        $this->assertFileExists(
            $schemaFile,
            'Schema file does not exist: ' . $schemaFile
        );

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
            'table xml_schema_test should not be found in the database'
        );
    }
}
