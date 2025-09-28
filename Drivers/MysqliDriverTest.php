<?php
/**
 * Tests cases for the mysqli driver of ADOdb.
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

use PHPUnit\Framework\TestCase;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
#[RequiresPhpExtension('mysqli')]
class MysqliDriverTest extends ADOdbTestCase
{
    
    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();

        if ($this->adoDriver !== 'mysqli') {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'This test is only applicable for the mysqli driver'
            );
        }
        
    }

    public function testSetCustomMetaType() : void
    {
        /*
        * We must define the custom type before loading the data dictionary
        */
        $ok = $this->db->setCustomMetaType('J', MYSQLI_TYPE_JSON, 'JSON');

        $this->assertTrue(
            $ok,
            'setCustomMetaType() should successfully append a JSON metaType'
        );
    }

    public function testCreateTableWithCustomMetaType(): void 
    {
        
        /*
        * Then create a data dictionary object, using this connection
        */
        
        $sql = "DROP TABLE IF EXISTS mt_test";

        list ($response,$errno,$errmsg) = $this->executeSqlString($sql);    
               
        $tabname = "mt_test";
        $flds = " 
        COL1 I  NOTNULL AUTO PRIMARY,
        CUSTOM_JSON_COLUMN J
        ";
        
        $sqlArray = $this->dataDictionary->createTableSQL(
            'mt_test', 
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

    public function testChangeTableWithCustomMetaType(): void 
    {
        
                      
        $tabname = "mt_test";
        $flds = " 
        ADDITIONAL_JSON_COLUMN J
        ";
        
        $sqlArray = $this->dataDictionary->changeTableSQL(
            'mt_test', 
            $flds
        );

        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);
       
        if ($errno > 0) {
            $this->fail(
                'Error adding additional custom meta type'
            );
        }

        $metaColumns = $this->db->metaColumns('mt_test');


        $this->assertArrayHasKey(
            'ADDITIONAL_JSON_COLUMN', 
            $metaColumns, 
            'createTableSQL() should have added ADDITIONAL_JSON_COLUMN'
        );

    }
}