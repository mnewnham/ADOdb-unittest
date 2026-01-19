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

namespace MNewnham\ADOdbUnitTest\CoreModule;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class MoveTest
 *
 * Test cases for Move methods
 */
class MoveTest extends ADOdbTestCase
{
    protected ?array $comparisonData = null;
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

        if (!$table3DataExists) {
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

        $db->setFetchMode(ADODB_FETCH_ASSOC);

        $setupSql = "SELECT * FROM testtable_3 ORDER BY id";

        $comparisonData = $db->getAll($setupSql);
        foreach ($comparisonData as $cd) {
            $obj = new \ADOFetchObj();
            foreach ($cd as $k => $v) {
                $k = strtolower($k);
                $obj->$k = $v;
            }
            $GLOBALS['comparisonData'][] = $obj;
        }

        $GLOBALS['moveRecordSet'] = $db->execute($setupSql);
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

        $this->comparisonData = $GLOBALS['comparisonData'];

        $this->recordCount       = count($this->comparisonData);
        $this->lastRecordOffset  = $this->recordCount - 1;

        $this->moveRecordSet = &$GLOBALS['moveRecordSet'];
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

        $this->assertFalse(
            $this->moveRecordSet->EOF,
            'move(0) should set EOF to false'
        );

        $this->assertFalse(
            $this->moveRecordSet->BOF,
            'move(0) should set BOF to false'
        );

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the first row should be returned as an object'
        );

        $this->assertSame(
            serialize($row),
            serialize($this->comparisonData[0]),
            'The row should match the first record of comparisonData'
        );

        $currentRow = $this->moveRecordSet->currentRow();

        $this->assertSame(
            $currentRow,
            0,
            'Currentrow() should return a value of zero at the first record'
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

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the last row should be returned as an object'
        );

        $this->assertSame(
            serialize($row),
            serialize($this->comparisonData[$this->lastRecordOffset]),
            'The row should match the last record of comparisonData'
        );

        $this->assertFalse(
            $this->moveRecordSet->EOF,
            'recordset EOF should return false if at last record of recordset'
        );

        $this->assertFalse(
            $this->moveRecordSet->BOF,
            'recordset EOF should return false if at last record of recordset'
        );

        $currentRow = $this->moveRecordSet->currentRow();

        $this->assertSame(
            $currentRow,
            $this->lastRecordOffset,
            'Currentrow() should retue\rn a value of lastrecord at the first record'
        );
    }

    /**
     * Test for moving to end of recordset when in EOF state
     *
     * @return void
     *
     *  *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     *
     */
    public function testMoveToEndAgain(): void
    {

        $success = $this->moveRecordSet->move($this->lastRecordOffset);

        $this->assertTrue(
            $success,
            'move() should still return true if going to EOF again'
        );

        $this->assertFalse(
            $this->moveRecordSet->EOF,
            'recordset EOF should still return false if at last record of recordset'
        );

        $this->assertFalse(
            $this->moveRecordSet->BOF,
            'recordset BOF should still return false if at last record of recordset'
        );

        $currentRow = $this->moveRecordSet->currentRow();

        $this->assertSame(
            $currentRow,
            $this->lastRecordOffset,
            'Currentrow() should retue\rn a value of lastrecord at the first record'
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

        $EOF = $this->moveRecordSet->EOF;

        $this->assertTrue(
            $EOF,
            'recordset EOF should return true if going past EOF'
        );

        $this->assertFalse(
            $this->moveRecordSet->BOF,
            'recordset BOF should still return false if at last record of recordset'
        );

        $currentRow = $this->moveRecordSet->currentRow();

        $this->assertFalse(
            $currentRow,
            'Currentrow() should return false if the record is outside the recordset range'
        );
    }

    /**
     * Test for moving further off end of recordset
     *
     * @return void
     *
     *  *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:move
     *
     */
    public function testMovePastEndAgain(): void
    {

        $success = $this->moveRecordSet->move($this->lastRecordOffset + 2);

        $this->assertFalse(
            $success,
            'move() should return false if going further past EOF'
        );

        $EOF = $this->moveRecordSet->EOF;

        $this->assertTrue(
            $EOF,
            'recordset EOF should return true if going further past EOF'
        );

        $this->assertFalse(
            $this->moveRecordSet->BOF,
            'recordset BOF should still return false if going further past EOF'
        );

        $currentRow = $this->moveRecordSet->currentRow();

        $this->assertFalse(
            $currentRow,
            'Currentrow() should return false if the record is outside the recordset range'
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
    public function testMoveBackToEnd(): void
    {

        $success = $this->moveRecordSet->move($this->lastRecordOffset);

        $this->assertTrue(
            $success,
            'move() should sucessfully move back to the last record ' .
            'in the set after overshooting'
        );

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the last row should be returned as an array after overshooting'
        );

         $EOF = $this->moveRecordSet->EOF;

        $this->assertFalse(
            $EOF,
            'recordset EOF should return false if going back past EOF'
        );

        $this->assertFalse(
            $this->moveRecordSet->BOF,
            'recordset BOF should return false if going back past EOF'
        );


        $this->assertSame(
            serialize($row),
            serialize($this->comparisonData[$this->lastRecordOffset]),
            'The row should match the last record of comparisonData' .
            'after overshooting'
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
    public function testMoveBackwardsFromEnd(): void
    {

        $success = $this->moveRecordSet->move($this->lastRecordOffset - 1);


        $this->assertTrue(
            $success,
            'move() should sucessfully move backwards from the last record ' .
            'in the set after overshooting'
        );

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the row should be returned as an object'
        );

        $EOF = $this->moveRecordSet->EOF;

        $this->assertFalse(
            $EOF,
            'recordset EOF should return false if going back past EOF'
        );

        $this->assertFalse(
            $this->moveRecordSet->BOF,
            'recordset BOF should return false if going back past EOF'
        );


        $this->assertSame(
            serialize($row),
            serialize($this->comparisonData[$this->lastRecordOffset - 1 ]),
            'The row should match the record of comparisonData'
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


    public function testMoveToNegativeOffset(): void
    {

        $success = $this->moveRecordSet->move(-1);

        $this->assertFalse(
            $success,
            'move() should return false if going past Beginning of File'
        );

        $this->assertFalse(
            $this->moveRecordSet->EOF,
            'recordset EOF should return false if going past Beginning of File'
        );

        $this->assertTrue(
            $this->moveRecordSet->BOF,
            'recordset BOF should return true if going past Beginning of File'
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
    public function testMoveToEvenMoreNegativeOffset(): void
    {

        $success = $this->moveRecordSet->move(-2);

        $this->assertFalse(
            $success,
            'move() should return false if going futher past Beginning of File'
        );

        $this->assertFalse(
            $this->moveRecordSet->EOF,
            'recordset EOF should return false if going past Beginning of File'
        );

        $this->assertTrue(
            $this->moveRecordSet->BOF,
            'recordset BOF should return true if going further past Beginning of File'
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
    public function testMoveBackToStartOffset(): void
    {

        $success = $this->moveRecordSet->move(0);

        $this->assertTrue(
            $success,
            'move() should sucessfully move to the first record in ' .
            ' the set after accessing a negative offset'
        );

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the first row should be returned as an object ' .
            'after a negative offset'
        );

        $this->assertSame(
            serialize($row),
            serialize($this->comparisonData[0]),
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

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the second row should be returned as an object ' .
            'after moveNext'
        );

        $this->assertSame(
            serialize($row),
            serialize($GLOBALS['comparisonData'][1]),
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

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the third row should be returned as an array ' .
            'after moveNext'
        );

        $this->assertSame(
            serialize($row),
            serialize($GLOBALS['comparisonData'][2]),
            'The row should match the third record of comparisonData ' .
            'after moveNext'
        );
    }

    /**
     * Test for {@see ADODConnection::moveLast()]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:moveend
     */
    public function testMoveToLast(): void
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

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the last row should be returned as an array ' .
            'after moveLast'
        );

        $this->assertSame(
            serialize($row),
            serialize($this->comparisonData[$this->lastRecordOffset]),
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

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the firs row should be returned as an array ' .
            'after moveFirst()'
        );

        $this->assertSame(
            serialize($row),
            serialize($this->comparisonData[0]),
            'The row should match the first record of comparisonData ' .
            'after moveLast'
        );
    }

    /**
     * Test for {@see ADODConnection::moveNext() when iteration moves
     * pointer off end of set]
     *
     * @return void
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:moveNext
     */
    public function testMoveNextOffEnd(): void
    {

        $success = $this->moveRecordSet->move($this->lastRecordOffset - 1);
        /*
        * Move pointer forward one, should be last record
        */
        $success = $this->moveRecordSet->moveNext();

        $this->assertTrue(
            $success,
            'moveNext() should sucessfully move to the last record in ' .
            ' the set'
        );

        $row = $this->moveRecordSet->fetchObj();

        $this->assertIsObject(
            $row,
            'the last row should be returned as an object ' .
            'after moveNext()'
        );

        $this->assertSame(
            serialize($row),
            serialize($this->comparisonData[$this->lastRecordOffset]),
            'The row should match the last record of comparisonData ' .
            'after moveLast'
        );

        /*
        * Move pointer forward one, should be past last record
        */
        $success = $this->moveRecordSet->moveNext();

        $this->assertFalse(
            $success,
            'moveNext() should return false after moving past EOF'
        );

        $row = $this->moveRecordSet->fetchObj();

        $this->assertFalse(
            $row,
            'the row off end should be returned as false ' .
            'after moveNext()'
        );

        $this->assertTrue(
            $this->moveRecordSet->EOF,
            'moveNext() should return should set the EOF flag after moving past EOF'
        );
    }
}
