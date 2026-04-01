<?php

/**
 * Tests cases for Dictionary Table Comments functions of ADODb
 *
 * This file is part of ADOdb-unittest, a PHPUnit test suite for
 * the ADOdb Database Abstraction Layer library for PHP.
 *
 * PHP version 8.0.0+
 *
 * @category  Library
 * @package   ADOdb-unittest
 * @author    Mark Newnham <mnewnham@github.com>
 * @copyright 2026 Mark Newnham
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 *
 * @link https://github.com/mnewnham/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

namespace MNewnham\ADOdbUnitTest\DataDict\Commments;

use MNewnham\ADOdbUnitTest\DataDict\DataDictFunctions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class AddColumnTest
 *
 * Test cases for for ADOdb DataDictAddColumn
 */
class TableCommentTest extends DataDictFunctions
{

    protected $commentTable = 'testtable_1';
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
    }

    public function setup(): void {

        parent::setup();

        if (!method_exists($this->dataDictionary, 'setTableCommentSql')) {
            $this->markTestSkipped(
                'This version of ADOdb does not support setting Table Comments'
            );
            $this->skipFollowingTests = true;
            return;
        }

    }


    /**
     * Test for {@see ADODConnection::setTableCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:setTableCommentSql
     *
     * @return void
     */
    public function testSetTableCommentSql(): void
    {

        $sql = $this->dataDictionary->setTableCommentSQL($this->commentTable, '1234567890');

        if ($sql === null) {
            $this->markTestSkipped(
                'setTableCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }

        $this->assertIsInt(
            strpos($sql,'1234567890'),
            sprintf('The returned SQL [%s] should contain "1234567890', $sql)
        );            
        
        if ($GLOBALS['DriverControl']->commentsRequireTransactions) {
            $this->db->startTrans();
        }
        
        $this->db->execute($sql);

        if ($GLOBALS['DriverControl']->commentsRequireTransactions) {
            $this->db->completeTrans();
        }

    }

    /**
     * Test for {@see ADODConnection::getTableCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getTableCommentSql
     *
     * @return void
     */
    public function testGetTableCommentSql(): void
    {

        $sql = $this->dataDictionary->getTableCommentSQL($this->commentTable);

        if ($sql === null) {
            $this->markTestSkipped(
                'getTableCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }
        
        $tableComment = $this->db->getOne($sql);

        $this->assertADOdbError($sql);

        $this->assertSame(
            '1234567890',
            $tableComment,
            'The table comment should be 123456789'
        );

    }
    /**
     * Test for {@see ADODConnection::setTableCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:setTableCommentSql
     *
     * @return void
     */
    public function testResetTableCommentSql(): void
    {

        $sql = $this->dataDictionary->setTableCommentSQL($this->commentTable, '2345678901');

        if ($sql === null) {
            $this->markTestSkipped(
                'setTableCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }

        $this->assertIsInt(
            strpos($sql,'2345678901'),
            sprintf('The returned SQL [%s] should contain "2345678901', $sql)
        );            
        
        if ($GLOBALS['DriverControl']->commentsRequireTransactions) {
            $this->db->startTrans();
        }
        
        $this->db->execute($sql);

        if ($GLOBALS['DriverControl']->commentsRequireTransactions) {
            $this->db->completeTrans();
        }

    }

    /**
     * Test for {@see ADODConnection::getTableCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getTableCommentSql
     *
     * @return void
     */
    public function testReGetTableCommentSql(): void
    {

        $sql = $this->dataDictionary->getTableCommentSQL($this->commentTable);

        if ($sql === null) {
            $this->markTestSkipped(
                'getTableCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }
        
        $tableComment = $this->db->getOne($sql);

        $this->assertADOdbError($sql);

        $this->assertSame(
            '2345678901',
            $tableComment,
            'The table comment should now be 2345678901'
        );

    }
}
