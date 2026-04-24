<?php

/**
 * Base Tests cases for driver specific string quoting
 *
 * This file is part of ADOdb-unittest, a PHPUnit test suite for
 * the ADOdb Database Abstraction Layer library for PHP.s
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

namespace MNewnham\ADOdbUnitTest\Drivers;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class ADOdbStringQuoting
 * Base Class for custom driver tests
 */

class StringQuotingTest extends ADOdbTestCase
{
    /**
     * The original string value to manipulate
     *
     * @var string
     */
    protected string $qStrInboundValue = "Famed author James O'Sullivan";

    /**
     * The expected result from the qstr test which has
     * database-specific escaping. This is a reasonable default
     *
     * @var     string $qStrExpectedResult
     */
    protected string $qStrExpectedResult = "";

    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();
        $this->qStrExpectedResult = $GLOBALS['DriverControl']->qStrExpectedResult;
   
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
        * Blank out the empty_field column first to ensure that
        * the total number of rows updated is correct
        */
        $SQL = "UPDATE testtable_3 SET empty_field = null";

        list($result, $errno, $errmsg) = $this->executeSqlString($SQL);

        if ($errno > 0) {
            return;
        }

        $qStrInboundValue = $this->db->qstr($this->qStrInboundValue);

        /*
        * Check that the escaping is correct
        */
        $this->assertSame(
            $qStrInboundValue,
            "'$this->qStrExpectedResult'",
            'The qstr() method should escape the inbound string correctly'
        );



        $SQL = "UPDATE testtable_3 SET empty_field = $qStrInboundValue";

        list($result, $errno, $errmsg) = $this->executeSqlString($SQL);

        if ($errno > 0) {
            return;
        }

        $expectedValue = 11;
        $actualValue = $this->getAffectedRows();

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

        $this->assertSame(
            $this->qStrInboundValue,
            $returnValue,
            'Qstr should have returned a string with the apostrophe ' .
            'set back to normal after retrieval from DB'
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
        $this->db->param(false);
        $p1 = $this->db->param('p1');
        $bind = array(
            'p1' => $this->db->addQ($this->qStrInboundValue)
        );

        $sql = "UPDATE testtable_3 SET empty_field = $p1";

        list($result, $errno, $errmsg) = $this->executeSqlString($sql, $bind);

        if ($errno > 0) {
            return;
        }

        $affectedRows =  $this->getAffectedRows();

        list($errno, $errmsg) = $this->assertADOdbError('Affected_Rows()');

        // We should have updated 11 rows
        $this->assertSame(
            11,
            $affectedRows,
            'All rows should have been updated with the test string'
        );

        // Now we will check the value in the empty_field column
        $sql = "SELECT empty_field FROM testtable_3";

        $returnValue = $this->db->getOne($sql);

        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $GLOBALS['DriverControl']->qStrExpectedResult,
            $returnValue,
            'addQ should have returned a string with the apostrophe ' .
            'set back to normal after retrieval from DB'
        );
    }

    /**
     * Test for {@see ADODConnection::qstr()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:qstr
     *
     * @return void
     */
    public function testSendNullToQstr(): void
    {
        /*
        * Blank out the empty_field column first to ensure that
        * the total number of rows updated is correct
        */
        $SQL = "UPDATE testtable_3 SET empty_field = null";

        $this->db->startTrans();
        
        $this->db->execute($SQL);
        
        $this->db->completeTrans();
  
        $qStrInboundValue = $this->db->qstr(null);
       
        /*
        * Check that the escaping is correct
        */
        $this->assertSame(
            $qStrInboundValue,
            "''",
            'The qstr() method should escape the inbound string correctly'
        );

        $SQL = "UPDATE testtable_3 SET empty_field = $qStrInboundValue";

        list($result, $errno, $errmsg) = $this->executeSqlString($SQL);

        if ($errno > 0) {
            return;
        }

        $expectedValue = 11;
        $actualValue = $this->getAffectedRows();

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


        $this->assertSame(
            $qStrInboundValue,
            "'$returnValue'",
            'Qstr should have returned a string with the apostrophe ' .
            'set back to normal after retrieval from DB'
        );
    }

    /**
     * Test for {@see ADODConnection::addq()}
     *
     * @link   https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:addq
     * @return void
     */
    public function testSendNullToAddq(): void
    {

        /*
        * The expected result is db dependent, so we will
        * insert the string into the empty_field column
        * and see if it fails to insert or not.
        */
        $this->db->param(false);
        $p1 = $this->db->param('p1');
        $bind = array(
            'p1' => $this->db->addQ(null)
        );

        $sql = "UPDATE testtable_3 SET empty_field = $p1";

        list($result, $errno, $errmsg) = $this->executeSqlString($sql, $bind);

        if ($errno > 0) {
            return;
        }

        $affectedRows =  $this->getAffectedRows();

        list($errno, $errmsg) = $this->assertADOdbError('Affected_Rows()');

        // We should have updated 11 rows
        $this->assertSame(
            11,
            $affectedRows,
            'All rows should have been updated with the test string'
        );

        $qStrInboundValue = $this->db->qstr(null);

        // Now we will check the value in the empty_field column
        $sql = "SELECT empty_field FROM testtable_3";

        $returnValue = $this->db->getOne($sql);

        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertSame(
            $qStrInboundValue,
            "'$returnValue'",
            'addQ should have returned a string with the apostrophe ' .
            'set back to normal after retrieval from DB'
        );
    }
}
