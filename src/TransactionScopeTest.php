<?php

/**
 * Tests cases for transaction scope handling functions of ADOdb
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
use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\TestCase;

/**
 * Class TransactionScopeTest
 *
 * Test cases for for ADOdb Tranaction Scope functionality
 */
class TransactionScopeTest extends ADOdbTestCase
{
    protected ?object $db;
    protected ?string $adoDriver;
    protected ?object $dataDictionary;

    protected bool $skipFollowingTests = false;

    /**
     * Set up the test environment first time
     *
     * @return void
     */
    public static function setupBeforeClass(): void
    {

        $db        = $GLOBALS['ADOdbConnection'];
        $adoDriver = $GLOBALS['ADOdriver'];

        /*
        * Fixes previously damaged transactions if necessary
        */
        $db->startTrans();
        $SQL = "UPDATE testtable_3 SET varchar_field='LINE 1' WHERE varchar_field IS NULL";
        $db->execute($SQL);
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

        if (!$this->db->hasTransactions) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'This database driver does not support transactions'
            );
        }
    }

    /**
     * Tests the smart transaction handling capabilities
     *
     * @return void
     */
    public function testStartCompleteTransaction(): void
    {
        
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping testStartCompleteTransaction as it ' .
                'is not applicable for the current driver'
            );
        }

        foreach ($this->testFetchModes as $fetchMode => $fetchDescription) {

            $this->db->setFetchMode($fetchMode);

            if ($fetchMode == ADODB_FETCH_NUM) {
                $idField = 0;
                $vcField = 1;
            }
            else if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
                $idField = 'ID';
                $vcField = 'VARCHAR_FIELD';
            } else {
                $idField = 'id';
                $vcField = 'varchar_field';
            }

            $this->db->StartTrans();

            $assertion = $this->assertEquals(
                1,
                $this->db->transCnt,
                sprintf(
                    '[%s] Transaction did not start correctly, ' .
                    'transCnt should be equal to 1',
                    $fetchDescription
                )
            );

            $sql = "SELECT id, varchar_field
                      FROM testtable_3 
                  ORDER BY id";

            $baseData = $this->db->getRow($sql);

            list($errno, $errmsg) = $this->assertADOdbError($sql);

            if ($errno > 0) {
                return;
            }

            $this->assertSame(
                $baseData[$vcField],
                'LINE 1',
                sprintf(
                    '[%s] Test should start with field initialized to "LINE 1"',
                    $fetchDescription
                )
            );

            $sql = "UPDATE testtable_3 
                    SET varchar_field = 'transaction test' 
                    WHERE id = 1";

            list($result, $errno, $errmsg) = $this->executeSqlString($sql, null, false);

            if ($errno > 0) {
                $this->failTest(
                   sprintf(
                        '[%s] Cannot set varchar_field error [%s] %s',
                        $fetcDescription,
                        $errno,
                        $errmsg
                    )
                );
                return;
            }

            /*
            * Check that the data has been updated in the transaction
            */
            $sql = "SELECT varchar_field 
                    FROM testtable_3 
                    WHERE id = {$baseData[$idField]}";
            $preCommit = $this->db->getOne($sql);
            
            list($errno, $errmsg) = $this->assertADOdbError($sql);

            if ($errno > 0) {
                return;
            }

            $this->assertEquals(
                'transaction test',
                $preCommit,
                sprintf(
                    '[%s] Data should be updated in the transaction',
                    $fetchDescription
                )
            );

            /*
            * Now we will rollback the transaction
            */
            $this->assertEquals(
                1,
                $this->db->transOff,
                sprintf(
                    '[%s] Smart Transactions should not ' . 
                    'be interspersed woth standard transactions',
                    $fetchDescription
                )
            );
            

            /*
            * Test the transaction to rollback at completion
            */
            $this->db->failTrans();

            list($errno, $errmsg) = $this->assertADOdbError('failTrans()');

            if ($errno > 0) {
                return;
            }

            $this->assertEquals(
                1,
                $this->db->transCnt,
                sprintf(
                    '[%s] Transaction count still should be 1 after rolling back the ' .
                    'transaction but before the completeTrans()',
                    $fetchDescription
                )
            );

            $sql = "SELECT varchar_field 
                    FROM testtable_3 
                    WHERE id = {$baseData[$idField]}";
            
            $postRollback = $this->db->getOne($sql);

            list($errno, $errmsg) = $this->assertADOdbError($sql);

            if ($errno > 0) {
                return;
            }

            $this->assertEquals(
                'transaction test',
                $postRollback,
                sprintf(
                    '[%s] Data should still be the updated value ' .
                    'after rolling back the transaction',
                    $fetchDescription
                )
            );

            $transactionFailed = $this->db->hasFailedTrans();

            $this->assertTrue(
                $transactionFailed,
                'hasFailedTrans() should report true'
            );


            $this->db->CompleteTrans();
            list($errno, $errmsg) = $this->assertADOdbError('completeTrans()');

            if ($errno > 0) {
                return;
            }
            
            $assertion = $this->assertEquals(
                0,
                $this->db->transCnt,
                sprintf(
                    '[%s] Transaction count $transCnt should now equal 0 ' .
                    'after completing the transaction',
                    $fetchDescription
                )
            );

            if ($this->db->transCnt > 0) {
                $this->fail(
                    sprintf(
                        '[%s] Trans Count shoud be 0 but is %d',
                        $fetcDescription,
                        $this->db->transCnt
                    )
                );
                return;
            }

            $sql = "SELECT varchar_field 
                    FROM testtable_3 
                    WHERE id = {$baseData[$idField]}";

            $postCommit = $this->db->getOne($sql);
            list($errno, $errmsg) = $this->assertADOdbError($sql);

            if ($errno > 0) {
                return;
            }

            $this->assertEquals(
                $baseData[$vcField],
                $postCommit,
                sprintf(
                    '[%s] Data should now be reverted to the original ' .
                    'value after committing the transaction',
                    $fetchDescription
                )
            );
        }
    }

    /**
     * Test beginning a transaction, committing it, and checking the data
     *
     * @return void
     */
    public function testBeginCommitTransaction(): void
    {
        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping testBeginCommitTransaction as it ' .
                'is not applicable for the current driver'
            );
        }

        foreach ($this->testFetchModes as $fetchMode => $fetcDescription) {

            $this->db->setFetchMode($fetchMode);

            if ($fetchMode == ADODB_FETCH_NUM) {
                $idField = 0;
                $vcField = 1;
            }
            else if (ADODB_ASSOC_CASE == ADODB_ASSOC_CASE_UPPER) {
                $idField = 'ID';
                $vcField = 'VARCHAR_FIELD';
            } else {
                $idField = 'id';
                $vcField = 'varchar_field';
            }

            $this->db->beginTrans();

            $assertion = $this->assertEquals(
                1,
                $this->db->transCnt,
                sprintf(
                    '[%s] Transaction did not start correctly,' .
                    'transCnt should be equal to 1',
                    $fetcDescription
                 )
            );

            $sql = "SELECT id, varchar_field 
                    FROM testtable_3 
                ORDER BY id";

            $baseData = $this->db->getRow($sql);
            list($errno, $errmsg) = $this->assertADOdbError($sql);

            if ($errno > 0) {
                return;
            }


            $sql = "UPDATE testtable_3 
                    SET varchar_field = 'transaction test' 
                    WHERE id = 1";

            $this->db->execute($sql);
            list($result, $errno, $errmsg) = $this->executeSqlString($sql, null, false);

            if ($errno > 0) {
                return;
            }

            /*
            * Check that the data has been updated in the transaction
            */
            $sql = "SELECT varchar_field 
                    FROM testtable_3 
                    WHERE id = {$baseData[$idField]}";
            $preCommit = $this->db->getOne($sql);

            list($errno, $errmsg) = $this->assertADOdbError($sql);

            if ($errno > 0) {
                return;
            }

            $this->assertEquals(
                'transaction test',
                $preCommit,
                 sprintf(
                    '[%s] VARCHAR_FIELD Data should have been updated ' .
                    'in the transaction before commit',
                    $fetcDescription
                 )
            );

            $this->db->rollbackTrans();

            
            $this->db->CommitTrans();
            
            
            list($errno, $errmsg) = $this->assertADOdbError('commitTrans()');
            /*
            * Check that the data has been rolled back in the transaction
            */
            $sql = "SELECT varchar_field 
                    FROM testtable_3 
                    WHERE id = {$baseData[$idField]}";

            $postCommit = $this->db->getOne($sql);

            list($errno, $errmsg) = $this->assertADOdbError($sql);

            if ($errno > 0) {
                return;
            }
            $this->assertEquals(
                $baseData[$vcField],
                $postCommit,
                 sprintf(
                    '[%s] VARCHAR_FIELD Data should now be rolled back ' .
                    'in the transaction after commit',
                    $fetcDescription
                 )
            );
        }
    }
}
