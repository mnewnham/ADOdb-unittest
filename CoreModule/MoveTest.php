<?php
/**
 * Tests cases for core move() methods
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
 * Class MoveTest
 *
 * Test cases for Move methods
 */
class MoveTest extends ADOdbTestCase
{

    protected ?array  $comparisonData = null;
    protected ?object $moveRecordSet  = null;

    protected int $recordCount      = 0;
    protected int $lastRecordOffset = 0;

    protected string $setupSql = "SELECT * FROM testtable_3 ORDER BY id";
    
    /**
     * Set up the test environment first time
     *
     * @return void
     */
    public static function setupBeforeClass(): void
    {

        $db        = $GLOBALS['ADOdbConnection'];
        $adoDriver = $GLOBALS['ADOdriver'];

        $SQL = "SELECT COUNT(*) AS core_table3_count FROM testtable_3";
        $table3DataExists = $db->getOne($SQL);

        if ($table3DataExists) {
            // Data already exists, no need to reload
            return;
        }

        /*
        *load Data into the table
        */
        $db->startTrans();

        $table3Data = sprintf('%s/DatabaseSetup/table3-data.sql', dirname(__FILE__));
        $table3Sql = file_get_contents($table3Data);
        $t3Sql = explode(';', $table3Sql);
        foreach ($t3Sql as $sql) {
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
        parent::setup();


        if (is_array($this->comparisonData)) {
            return;
        }

        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
   
        $this->comparisonData = $this->db->getAll($this->setupSql);
        

        $this->recordCount       = count($this->comparisonData);
        $this->lastRecordOffset  = $this->recordCount - 1;

        list(
            $this->moveRecordSet,
            $errno,
            $errmsg
        ) = $this->executeSqlString($this->setupSql);

    }
 
    /**
     * Test for {@see ADODConnection::move()]
     * 
     * @return void0
     * 
     *  * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     * 
     */
    public function testMoveToStart(): void
    {
       
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $success = $this->moveRecordSet->move(0);
        
        $this->assertTrue(
            $success,
            'move(0) should sucessfully move to the first record in the set'
        );
       
        $row = $this->moveRecordSet->fetchRow();

        $this->assertIsArray(
            $row,
            'the first row should be returned as an array'
        );

        $this->assertSame(
            $row,
            $this->comparisonData[0],
            'The row should match the first record of comparisonData'
        );
    }
    
    /**
     * Test for move directly to end of recordset
     * 
     * @return void
     * 
     *  * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     * 
     */
    public function testMoveToEnd(): void
    {

        $success = $this->moveRecordSet->move($this->lastRecordOffset);
        
        $this->assertTrue(
            $success,
            'move([lastno]) should sucessfully move to the last record in the set'
        );
       
        $row = $this->moveRecordSet->fetchRow();

        $this->assertIsArray(
            $row,
            'the last row should be returned as an array'
        );

        $this->assertSame(
            $row,
            $this->comparisonData[$this->lastRecordOffset],
            'The row should match the last record of comparisonData'
        );
    }

    /**
     * Test for moving off end of recordset
     * 
     * @return void
     * 
     *  * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     * 
     */
    public function testMovePastEnd(): void
    {

        $success = $this->moveRecordSet->move($this->lastRecordOffset + 1);
        
        $this->assertFalse(
            $success,
            'move() should return false if going past EOF'
        );
      
        $success = $this->moveRecordSet->move($this->lastRecordOffset + 2);
        
        $this->assertFalse(
            $success,
            'move() should return false if going further past EOF'
        );

    }

    /**
     * Test for moving back to end of recordset
     * 
     * @return void
     * 
     *  * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     * 
     */
    public function moveBackToEnd(): void 
    {  
        
        $success = $this->moveRecordSet->move($this->lastRecordOffset);
        
        $this->assertTrue(
            $success,
            'move() should sucessfully move back to the last record ' . 
            'in the set after overshooting'
        );
       
        $row = $this->moveRecordSet->fetchRow();

        $this->assertIsArray(
            $row,
            'the last row should be returned as an array after overshooting'
        );

        $this->assertSame(
            $row,
            $this->comparisonData[$this->lastRecordOffset],
            'The row should match the last record of comparisonData' . 
            'after overshooting'
        );

    }

    /**
     * Test for moving off start of recordset
     * 
     * @return void
     * 
     *  * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     * 
     */
    public function moveToNegativeOffset() : void
    {
        
        $success = $this->moveRecordset->move(-1);
        
        $this->assertFalse(
            $success,
            'move() should return false if going past Begiining of File'
        );
    }

    /**
     * Test for moving back to start of recordset
     * 
     * @return void
     * 
     *  * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     */
    public function moveBackToStartOffset() : void
    {

        $success = $this->moveRecordSet->move(0);
        
        $this->assertTrue(
            $success,
            'move() should sucessfully move to the first record in ' . 
            ' the set after accessing a negative offset'
        );
       
        $row = $this->moveRecordSet->fetchRow();

        $this->assertIsArray(
            $row,
            'the first row should be returned as an array ' . 
            'after a negative offset'
        );

        $this->assertSame(
            $row,
            $this->comparisonData[0],
            'The row should match the first record of comparisonData ' . 
            'after accessing a negative offset'
        );

    }

    /**
     * Test for {@see ADODConnection::movenext()]
     * 
     * @return void
     * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     */
    public function testMoveNext(): void
    {
        
        /*
        * Start at beginning of recordset for test
        */
        $this->moveRecordSet->move(0);
        
        /*
        * Move pointer to record 1
        */
        $success = $this->moveRecordSet->moveNext();
        
        $this->assertTrue(
            $success,
            'moveNext() should sucessfully move to the second record in ' . 
            ' the set'
        );
       
        $row = $this->moveRecordSet->fetchRow();

        $this->assertIsArray(
            $row,
            'the second row should be returned as an array ' . 
            'after moveNext'
        );

        $this->assertSame(
            $row,
            $this->comparisonData[1],
            'The row should match the second record of comparisonData ' . 
            'after moveNext'
        );
    }

    /**
     * Test for {@see ADODConnection::movenext()]
     * 
     * @return void
     * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     */
    public function testMoveNextAgain(): void
    {
        
        $success = $this->moveRecordSet->moveNext();
        
        $this->assertTrue(
            $success,
            'moveNext() should sucessfully move to the third record in ' . 
            ' the set'
        );
       
        $row = $this->moveRecordSet->fetchRow();

        $this->assertIsArray(
            $row,
            'the third row should be returned as an array ' . 
            'after moveNext'
        );

        $this->assertSame(
            $row,
            $this->comparisonData[2],
            'The row should match the third record of comparisonData ' . 
            'after moveNext'
        );
       
    }

    /**
     * Test for {@see ADODConnection::moveend()]
     * 
     * @return void
     * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:moveend
     */
    public function testMoveEnd(): void
    {
        
        /*
        * Start at beginning of recordset for test
        */
        $this->moveRecordSet->move(0);
        
        /*
        * Move pointer at end of file
        */
        $success = $this->moveRecordSet->moveLast();
        
        $this->assertTrue(
            $success,
            'moveLast() should sucessfully move to the last record in ' . 
            ' the set'
        );
       
        $row = $this->moveRecordSet->fetchRow();

        $this->assertIsArray(
            $row,
            'the last row should be returned as an array ' . 
            'after moveLast'
        );

        $this->assertSame(
            $row,
            $this->comparisonData[$this->lastRecordOffset],
            'The row should match the last record of comparisonData ' . 
            'after moveLast'
        );
    }

    
    /**
     * Test for {@see ADODConnection::movefirst()]
     * 
     * @return void
     * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:movefirst
     */
    public function testMoveFirst(): void
    {
        
        /*
        * Move pointer  of fito start of fi`le
        */
        $success = $this->moveRecordSet->moveFirst();
        
        $this->assertTrue(
            $success,
            'moveFirst() should sucessfully move to the last record in ' . 
            ' the set'
        );
       
        $row = $this->moveRecordSet->fetchRow();

        $this->assertIsArray(
            $row,
            'the firs row should be returned as an array ' . 
            'after moveFirst()'
        );

        $this->assertSame(
            $row,
            $this->comparisonData[0],
            'The row should match the first record of comparisonData ' . 
            'after moveLast'
        );
    }

}