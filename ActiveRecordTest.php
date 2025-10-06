<?php
/**
 * Tests active record functions
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
class ActiveRecordTest extends ADOdbTestCase
{
   
        
    protected $personColumns = array(
        'name_first',
        'name_last',
        'birth_date'
    );
    
    /**
     * Sets up a flag used from refreshing the table mid-test
     *
     * @return void
     */
    public static function setupBeforeClass() : void {


        $db        = $GLOBALS['ADOdbConnection'];
        $adoDriver = $GLOBALS['ADOdriver'];

        if ($GLOBALS['skipActiveRecordTests'] == 1) {
            return;
        }

        /*
        *load Active record Table and Data into the table
        */
        $db->startTrans();
        
        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/active-record-schema.sql', 
            dirname(__FILE__), 
            $adoDriver
        );

        $tableSql = file_get_contents($tableSchema);
        $tSql = explode(';', $tableSql);
        foreach ($tSql as $sql) {
            if (trim($sql ?? '')) {
                $db->execute($sql);
            }
        }

        $tableData = sprintf(
            '%s/DatabaseSetup/active-record-data.sql', 
            dirname(__FILE__)
        );
        $tableSql = file_get_contents($tableData);
        $tSql = explode(';', $tableSql);
        foreach ($tSql as $sql) {
            if (trim($sql ?? '')) {
                $db->execute($sql);
            }
        }

        $db->completeTrans();
    }

    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        global $_ADODB_ACTIVE_DBS;
        $_ADODB_ACTIVE_DBS = array();
        if ($GLOBALS['skipActiveRecordTests'] == 1) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'This test must be deliberately activated'
            );
        }
        if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'ActiveRecord tests cannot be run if ADODB_ASSOC_CASE is UPPER'
            );          
        }
        parent::setup();
        /*
        * Activate the active record adaptor
        */
        //ADOdb_Active_Record::SetDatabaseAdapter($this->db);
        ADODB_SetDatabaseAdapter($this->db);

    }

    /**
     * Tests the ActiveRecord::getAttributes() method
     *
     * @return void
     */
    public function testGetAttributes() : void 
    {
        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');

        $person = $GLOBALS['person'] ;

        $attributes = $person->getAttributeNames();

        foreach ($this->personColumns as $column) {
            $ok = in_array($column,$attributes);
            $this->assertTrue(
                $ok,
                sprintf('Person object should have %s attribute', $column)
            );
        }

    }

    /**
     * Tests the ActiveRecord quoteNames() feature
     *
     * @return void
     */
    public function testQuoteNames() : void
    {

        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');

        $person = $GLOBALS['person'] ;//new \person();
        $person->_quoteNames = true;

        $person->load('id=1');

        foreach ($this->personColumns as $column) {
            $ok = property_exists($person,$column);
            $this->assertTrue(
                $ok,
                sprintf('Person object should have %s property after setting _quoteNames', $column)
            );
            
        }
    }

    /**
     * Tests loading a new parent record into the db
     *
     * @return void
     */
    public function testAddNewPerson() : void 
    {
        
        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');
       
        $person = $GLOBALS['person'] ;
        
        unset($person->id);
        $person->name_first  = 'SHEILA';
        $person->name_last   = 'BROVLOWSKI';
        $person->birth_date  = '1975-03-02';

   
        $this->db->startTrans();
        $person->insert();
        $newId = $person->LastInsertId($this->db, '');

        $this->db->completeTrans();
        
        list($errno,$errmsg) = $this->assertADOdbError('person->insert()');

        $this->assertEquals(
            $newId,
            3,
            'LastInsertId() of newly inserted record should return an id of 3'
        );

        $person->load("id=$newId");

        foreach ($this->personColumns as $column) {
            $ok = property_exists($person, $column);
            $this->assertTrue(
                $ok,
                sprintf(
                    'Person object should have %s property after insert', 
                    $column
                )
            );
            
        }


    }

    /**
     * Tests adding a new child record into the db
     *
     * @return void
     */
    public function testAddNewChild() : void {
        
        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');

       
        
        $child = $GLOBALS['child'] ;//new \child();

        unset($child->id);

        $child->person_id   = 3;
        $child->name_first  = 'STAN';
        $child->name_last   = 'BROVLOWSKI';
        $child->birth_date  = '2015-11-21';

        $this->db->startTrans();
        $child->insert(); 
        $this->db->completeTrans();

        list($errno,$errmsg) = $this->assertADOdbError('child->save()');

    }


    /**
     * Tests retrieving a parent record by id
     *
     * @return void
     */
    public function testLoadExistingPersonById() : void {
        
        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');

       
        $person = $GLOBALS['person'] ;//new \person();

        $person->load('id=2');

        list($errno,$errmsg) = $this->assertADOdbError('person->load()');

        $this->assertIsObject(
            $person,
            'Active Directory load() method should return an object'
        );

        foreach ($this->personColumns as $column) {
            $ok = property_exists($person,$column);
            $this->assertTrue(
                $ok,
                sprintf('Person object should have %s property after load by id', $column)
            );
            
        }

    }

    /**
     * Tests retieving a parent record by string match
     *
     * @return void
     */
    public function testLoadExistingPersonByMatch() : void {
        
        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');

       
        $person = $GLOBALS['person'] ;//new \person();

        $person->load("name_last LIKE 'CONN%'");

        list($errno,$errmsg) = $this->assertADOdbError('person->save()');

        $this->assertIsObject(
            $person,
            'Active Directory load() method should return an object'
        );

        foreach($this->personColumns as $column) {
            $ok = property_exists($person,$column);
            $this->assertTrue(
                $ok,
                sprintf('Person object should have %s property after load by match', $column)
            );
            
        }

    }

    /**
     * Test loading existing children of a know parent
     *
     * @return void
     */
    public function testLoadChildrenByRelation() : void {
        
        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');

       
        $person = $GLOBALS['person'] ;//new \person();

        $person->load("id=2");

        $person->LoadRelations(
            'children', 
            'order by id'
        );

        $this->assertEquals(
            3,
            sizeof($person->children),
            'Relations of Person should match loaded children' 
        );

    }

    /**
     * Test loading a subset of children for a know parnt
     *
     * @return void
     */
    public function testLoadChildrenByMatch() : void {
        
        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');

       
        $person = $GLOBALS['person'] ;//new \person();

        $person->load("id=3");

        $person->LoadRelations(
            'children', 
            "name_first LIKE 'S%' ORDER BY id"
        );

        $this->assertEquals(
            1,
            sizeof($person->children),
            'Relations of Person should match 1 loaded child' 
        );
    }

    /**
     * Tests writing back changes to child data as a subset of parent 
     *
     * @return void
     */
    public function testWriteChildData() : void {
        
        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');

        $person = $GLOBALS['person'] ;//new \person();

        $person->load("id=1");

        $person->LoadRelations(
            'children', 
            "id=1"
        );
      
        $this->assertEquals(
            1,
            sizeof($person->children),
            'Relations of Person should match 1 loaded child' 
        );

        $this->db->startTrans();
        $child = end($person->children);

        $child->name_first = 'STAN';

        $child->save();
        $this->db->completeTrans();

        $person->LoadRelations(
            'children', 
            "id=1"
        );

        $this->assertEquals(
            'STAN',
            $person->children[0]->name_first,
            'Save() should write back child property'
        );
    }


    public function testGetActiveRecordsIntoArray() : void 
    {

        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');
        
        $p1 = $this->db->param('p1');
        $p2 = $this->db->param('p2');
        $bind = array(
            'p1'=>0,
            'p2'=>5
        );

        $activeRecArray = $this->db->getActiveRecords
            ('persons',
            "id BETWEEN $p1 AND $p2",
            $bind
        ); 

        $this->assertEquals(
            count($activeRecArray),
            3,
            'getActiveRecords() should return all records in table'
        );

       
        foreach ($activeRecArray as $rec) {

            foreach ($this->personColumns as $column) {
                $ok = property_exists($rec,$column);
                $this->assertTrue(
                    $ok,
                    sprintf('Person object should have %s property after setting _quoteNames', $column)
                );
                
            }
        }           
    
    }

    /**
     * Uses the find() method to locate the class
     *
     * @return void
     */
    public function testFindMethod() : void
    {
        ADODB_Active_Record::TableHasMany('persons', 'children', 'person_id');

        $person = new $GLOBALS['person'];

        $result = $person->find('birth_date='. $this->db->dbDate('1975-03-03'));
       
        $this->assertIsArray(
            $result,
            'find() should return an array if successful'
        );

        foreach ($result as $rec) {

            foreach ($this->personColumns as $column) {
                $ok = property_exists($rec,$column);
                $this->assertTrue(
                    $ok,
                    sprintf('Person object should have %s property after executing get', $column)
                );
                
            }
        }           
        
    }

}