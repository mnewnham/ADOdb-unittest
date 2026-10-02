<?php
/**
 * Tests cases for returnungid when used in MariaDb
 * see https://github.com/ADOdb/ADOdb/pull/1252
 *
 * This file is part of ADOdb-unittest, a PHPUnit test suite for
 * the ADOdb Database Abstraction Layer library for PHP.
 *
 * PHP version 8.0.0+
 *
 * @category  Library
 * @package   ADOdb-unittest
 * @author    <mistericy@github.com>
 * @author    Mark Newnham <mnewnham@github.com>
 * @copyright 2025,2026 Mark Newnham
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 *
 * @link https://github.com/mnewnham/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */
namespace MNewnham\ADOdbUnitTest\Drivers\mysql;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\TestCase;

/**
 * Class MysqliReturningTest.
 *
 * Regression test for the mysqli driver's true prepared statement path:
 * a duplicate-key (or other constraint) violation on a bound
 * INSERT ... RETURNING statement is not reported by mysqli_stmt_execute() -
 * it only surfaces when the result set is materialized via get_result().
 * Before the fix, ErrorNo()/ErrorMsg() came back empty in that case even
 * though Execute() correctly returned false and no row was inserted.
 *
 * Requires a live MariaDB 10.5+ server (RETURNING support); the test is
 * skipped if one isn't reachable via the ADODB_TEST_MYSQLI_* env vars.
 */
class ReturningIdTest extends ADOdbTestCase
{

    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();

        $db        = $GLOBALS['ADOdbConnection'];
        $adoDriver = $GLOBALS['ADOdriver'];

        $info = $db->ServerInfo();
        if (stripos($info['description'], 'mariadb') === false) {
           return;
        }

        if ($GLOBALS['DriverControl']->supportsDropIfExists) {
            if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
                $db->startTrans();
            }

            $sql = "DROP TABLE IF EXISTS returning_id_test";
            $db->execute($sql);

            if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
                $db->completeTrans();
            }
        }

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->startTrans();
        }

        /*
        * load insert id test schema
        */

        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/returning-id-schema.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        /*
        * Loads the schema based on the DB type
        */

        readSqlIntoDatabase($db, $tableSchema);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $db->completeTrans();
        }
    }

    
    public function setUp(): void
    {
        
        $db = $GLOBALS['ADOdbConnection'];
        
        $info = $db->ServerInfo();
        if (stripos($info['description'], 'mariadb') === false) {
            $this->markTestSkipped('INSERT ... RETURNING requires MariaDB');
        }


        $db->Execute('DELETE FROM returning_id_test');

        $db->Execute(
            "INSERT INTO returning_id_test (id, name) VALUES (1, 'existing')"
        );
    }

    
    /**
     * A duplicate-key violation on a bound INSERT ... RETURNING must be
     * reported through ErrorNo()/ErrorMsg(), not just as a false return.
     */
    public function testDuplicateKeyOnReturningInsertReportsError(): void
    {

        $db = $GLOBALS['ADOdbConnection'];
        $rs = $db->Execute(
            'INSERT INTO returning_id_test (id, name) VALUES (?, ?) RETURNING id',
            [1, 'duplicate']
        );

        $this->assertFalse($rs, 'Execute() must fail on a duplicate key');
        $this->assertSame(1062, $db->ErrorNo(), 'ErrorNo() must report the duplicate-key error');
        $this->assertStringContainsString(
            'Duplicate entry',
            $db->ErrorMsg(),
            'ErrorMsg() must report the duplicate-key error'
        );

        $count = $db->GetOne(
            'SELECT COUNT(*) FROM returning_id_test WHERE id = 1'
            );
        $this->assertSame(1, (int) $count, 'The duplicate row must never be inserted');
    }

    /**
     * Control: a plain (non-RETURNING) bound INSERT must keep reporting the
     * error exactly as before this fix (see #872 / #876).
     */
    public function testDuplicateKeyOnPlainInsertReportsError(): void
    {
        $db = $GLOBALS['ADOdbConnection'];
        $rs = $db->Execute(
            'INSERT INTO returning_id_test (id, name) VALUES (?, ?)',
            [1, 'duplicate']
        );

        $this->assertFalse($rs);
        $this->assertSame(1062, $db->ErrorNo());
        $this->assertStringContainsString('Duplicate entry', $db->ErrorMsg());
    }

    /**
     * A successful bound INSERT ... RETURNING must still return a usable
     * recordset with the returned column(s).
     */
    public function testSuccessfulReturningInsertReturnsRow(): void
    {
        $db = $GLOBALS['ADOdbConnection'];
        $rs = $db->Execute(
            'INSERT INTO returning_id_test (id, name) VALUES (?, ?) RETURNING id',
            [2, 'new']
        );

        $this->assertNotFalse($rs);
        $row = $rs->FetchRow();
        $this->assertSame(2, (int) $row['id']);
    }
}