<?php

/**
 * Tests cases for Dictionary Index Comments functions of ADODb
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
 * Class IndexCommentTest
 *
 * Test cases for for ADOdb Commenting
 */
class IndexCommentTest extends DataDictFunctions
{

    protected $commentTable  = 'testtable_1';
    protected $commentIndex  = 'vdx1';
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
        if ($GLOBALS['ADOdriver'] == 'mysqli') {
            $GLOBALS['iCommentText'] = '1234567890';
        } else {
            $GLOBALS['iCommentText'] = md5(time());
        }
    }

    public function setup(): void {

        parent::setup();

        if (!$GLOBALS['DriverControl']->supportsIndexComments) {
            $this->markTestSkipped(
                'This Database does not support setting Index Comments'
            );
            $this->skipFollowingTests = true;
            return;
        }
        if (!method_exists($this->dataDictionary, 'setIndexCommentSql')) {
            $this->markTestSkipped(
                'This version of ADOdb does not support setting Index Comments'
            );
            $this->skipFollowingTests = true;
            return;
        }

    }

    /**
     * Test for {@see ADODConnection::setIndexCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:setIndexCommentSql
     *
     * @return void
     */
    public function testSetIndexCommentSql(): void
    {

        $sql = $this->dataDictionary->setIndexCommentSQL(
            $this->commentTable,
            $this->commentIndex,
            $GLOBALS['iCommentText']
        );

        if ($sql === null) {
            $this->markTestSkipped(
                'setIndexCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }

        $this->assertIsInt(
            strpos($sql,$GLOBALS['iCommentText']),
            sprintf(
                'The returned SQL [%s] should be "%s"', 
                $sql,
                $GLOBALS['iCommentText']
            )
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
     * Test for {@see ADODConnection::getIndexCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getIndexCommentSql
     *
     * @return void
     */
    public function testRegetIndexCommentSql(): void
    {

        $sql = $this->dataDictionary->getIndexCommentSQL(
            $this->commentTable,
            $this->commentIndex
        );

        if ($sql === null) {
            $this->markTestSkipped(
                'getIndexCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }
        
        $indexComment = $this->db->getOne($sql);

        $this->assertADOdbError($sql);

        $this->assertSame(
            $GLOBALS['iCommentText'],
            $indexComment,
            'The index comment should be ' . $GLOBALS['iCommentText']
        );

    }

    /**
     * Test for {@see ADODConnection::setIndexCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:setIndexCommentSql
     *
     * @return void
     */
    public function testResetIndexCommentSql(): void
    {

        $sql = $this->dataDictionary->setIndexCommentSQL(
            $this->commentTable,
            $this->commentIndex,
            'A' . $GLOBALS['iCommentText']
        );

        if ($sql === null) {
            $this->markTestSkipped(
                'setIndexCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }

        $this->assertIsInt(
            strpos($sql,'A' . $GLOBALS['iCommentText']),
            sprintf(
                'The returned SQL [%s] should contain "A%s',
                $sql,
                $GLOBALS['iCommentText']
            )
        );            
 
        $this->db->startTrans();
        $this->db->execute($sql);
        $this->db->completeTrans();
        
        if ($GLOBALS['DriverControl']->commentsRequireTransactions) {
            $this->db->startTrans();
        }
        
        $this->db->execute($sql);

        if ($GLOBALS['DriverControl']->commentsRequireTransactions) {
            $this->db->completeTrans();
        }
    }

    /**
     * Test for {@see ADODConnection::getIndexCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getIndexCommentSql
     *
     * @return void
     */
    public function testGetIndexCommentSql(): void
    {

        $sql = $this->dataDictionary->getIndexCommentSQL(
            $this->commentTable,
            $this->commentIndex
        );

        if ($sql === null) {
            $this->markTestSkipped(
                'getIndexCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }
        
        $indexComment = $this->db->getOne($sql);

        $this->assertADOdbError($sql);

        $this->assertSame(
            'A' . $GLOBALS['iCommentText'],
            $indexComment,
            'The index comment should now be ' . $GLOBALS['iCommentText']
        );

    }
}
