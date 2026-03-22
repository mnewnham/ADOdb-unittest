<?php

/**
 * Tests cases for Dictionary Column Comments functions of ADODb
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
 * Class ColumnCommentTest
 *
 * Test cases for for ADOdb Commenting
 */
class ColumnCommentTest extends DataDictFunctions
{

    protected string $commentTable  = 'testtable_1';
    protected string $commentColumn = 'varchar_field';

    protected string $commentText = '';
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
        $GLOBALS['commentText'] = md5(time());
    }

    public function setup(): void {

        parent::setup();

        if (!method_exists($this->dataDictionary, 'setColumnCommentSql')) {
            $this->markTestSkipped(
                'This version of ADOdb does not support setting Column Comments'
            );
            $this->skipFollowingTests = true;
            return;
        }
    }


    /**
     * Test for {@see ADODConnection::setColumnCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:setColumnCommentSql
     *
     * @return void
     */
    public function testSetColumnCommentSql(): void
    {

        $sql = $this->dataDictionary->setColumnCommentSQL(
            $this->commentTable,
            $this->commentColumn,
            $GLOBALS['commentText'],
            $GLOBALS['DriverControl']->columnCommentDefinitions
        );

        if ($sql === null) {
            $this->markTestSkipped(
                'setColumnCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }

        $this->assertIsInt(
            strpos($sql,$GLOBALS['commentText']),
            sprintf(
                'The returned SQL [%s] should contain "%s', 
                $sql, 
                $GLOBALS['commentText']
            )
        );            
 
        $this->db->startTrans();
        $this->db->execute($sql);
        $this->db->completeTrans();

    }

    /**
     * Test for {@see ADODConnection::getColumnCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getColumnCommentSql
     *
     * @return void
     */
    public function testGetColumnCommentSql(): void
    {

        $sql = $this->dataDictionary->getColumnCommentSQL(
            $this->commentTable,
            $this->commentColumn
        );

        if ($sql === null) {
            $this->markTestSkipped(
                'getColumnCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }
        
        $columnComment = $this->db->getOne($sql);

        $this->assertADOdbError($sql);

        $this->assertSame(
            $GLOBALS['commentText'],
            $columnComment,
            'The column comment should be ' . $GLOBALS['commentText']
        );

    }

    /**
     * Test for {@see ADODConnection::setColumnCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:setColumnCommentSql
     *
     * @return void
     */
    public function testResetColumnCommentSql(): void
    {

        $sql = $this->dataDictionary->setColumnCommentSQL(
            $this->commentTable,
            $this->commentColumn,
            'A' . $GLOBALS['commentText'],
            $GLOBALS['DriverControl']->columnCommentDefinitions
        );

        if ($sql === null) {
            $this->markTestSkipped(
                'setColumnCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }

        $this->assertIsInt(
            strpos($sql,$GLOBALS['commentText']),
            sprintf(
                'The returned SQL [%s] should contain "%s', 
                $sql, 
                'A' . $GLOBALS['commentText']
            )
        );            
 
        $this->db->startTrans();
        $this->db->execute($sql);
        $this->db->completeTrans();

    }

    /**
     * Test for {@see ADODConnection::getColumnCommentSql()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:getColumnCommentSql
     *
     * @return void
     */
    public function testReGetColumnCommentSql(): void
    {

        $sql = $this->dataDictionary->getColumnCommentSQL(
            $this->commentTable,
            $this->commentColumn
        );

        if ($sql === null) {
            $this->markTestSkipped(
                'getColumnCommentSql() not supported by driver'    
            );
            $this->skipFollowingTests = true;
            return;
        }
        
        $columnComment = $this->db->getOne($sql);

        $this->assertADOdbError($sql);

        $this->assertSame(
            'A' . $GLOBALS['commentText'],
            $columnComment,
            'The column comment should be A' . $GLOBALS['commentText']
        );

    }
}
