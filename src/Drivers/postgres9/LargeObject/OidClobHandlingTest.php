<?php

/**
 * Tests cases for CLOB I/O handling, such as large text files
 *
 * This file is part of ADOdb-unittest, a PHPUnit test suite for
 * the ADOdb Database Abstraction Layer library for PHP.
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

namespace MNewnham\ADOdbUnitTest\Drivers\postgres9\LargeObject;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;

/**
 * Class ClobHandlingTest
 *
 * Test cases for for Text Large Object
 */
class OidClobHandlingTest extends ADOdbTestCase
{
    protected ?string $testClobFile;

    protected string $testTableName = 'blob_storage_table';

    protected int $integerField = 9102;

    /**
     * Set up the test environment
     *
     * This method is called once before any tests are run.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        $testClobFile = $GLOBALS['TestingControl']['blob']['testClob'];
        if (!$testClobFile) {
            return;
        }
        if (!file_exists($testClobFile)) {
            return;
        }


        $db = $GLOBALS['ADOdbConnection'];
        /*
        * Load the table to test data length tests
        */
        $schemaFile = sprintf(
            '%s/DatabaseSetup/%s/blob-storage-table.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        $db->startTrans();
        $ok = readSqlIntoDatabase($db, $schemaFile);
        $db->completeTrans();

        $db->startTrans();
        $sql = "INSERT INTO blob_storage_table (integer_field) VALUES (9102)";
        $db->Execute($sql);
        $sql = "INSERT INTO blob_storage_table (integer_field) VALUES (9103)";
        $db->Execute($sql);

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

        if (!array_key_exists('testClob', $GLOBALS['TestingControl']['blob'])) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'The testClob setting is not defined in the adodb-unittest.ini file'
            );
        }

        $this->testClobFile = $GLOBALS['TestingControl']['blob']['testClob'];

        if (!$this->testClobFile) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'Clob sets will be skipped'
            );
        }

        if (!file_exists($this->testClobFile)) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'The testClob file does not exist: ' . $this->testClobFile
            );
        }
    }

    /**
     * Test for {@see updateBlob}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:updateblob
     *
     * @return void
     */
    public function testUpdateClob(): void
    {

        $clob = file_get_contents($this->testClobFile);

        $this->db->startTrans();

        $result = $this->db->updateBlob(
            $this->testTableName,
            'varchar_field',
            $clob,
            'integer_field=9102',
            'CLOB,OID'
        );

        list($errno, $errmsg) = $this->assertADOdbError('updateClob()');

        $this->db->completeTrans();

        $this->assertTrue(
            $result,
            'updateClob() should return true on success'
        );
    }

     /**
     * Test for {@see blobDecode} after BlobEncode
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:blobDecode
     *
     * @return void
     */
    public function testClobDecode(): void
    {

        if ($this->skipFollowingTests) {
            return;
        }

        $newFileArray = explode('.', $this->testClobFile);
        $extension = array_pop($newFileArray);
        $newFile = implode('.', $newFileArray) . '-decoded.' . $extension;

        $SQL = "SELECT varchar_field 
                  FROM {$this->testTableName} 
                 WHERE integer_field=9102";


        $clobSelect = $this->db->getOne($SQL);

        list($errno, $errmsg) = $this->assertADOdbError($SQL);

        $clob = $this->db->blobDecode($clobSelect, $maxsize = false, $hastrans = true, $blobtype = 'CLOB,OID');
        list($errno, $errmsg) = $this->assertADOdbError('blobDecode()');

        file_put_contents(
            $newFile,
            $clob
        );

        $this->assertFileExists(
            $newFile,
            'The clob file should have been written to ' . $newFile
        );

        /*
        * Do some filesystem checks
        */
        $originalFileSize = filesize($this->testClobFile);
        $decodedFileSize  = filesize($newFile);


        $this->assertSame(
            $originalFileSize,
            $decodedFileSize,
            'Clob Decoded file size after BlobEncode should match the original file size'
        );
    }

    /**
     * Test for {@see updateBlobFile}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:updateblobfile
     *
     * @return void
     */
    public function testUpdateClobFile(): void
    {

        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping testUpdateClob as the testClob setting ' .
                'is not defined in the adodb-unittest.ini file'
            );
            return;
        }

        $this->db->startTrans();

        $result = $this->db->updateBlobFile(
            $this->testTableName,
            'varchar_field',
            $this->testClobFile,
            'integer_field=9103',
            'CLOB,OID'
        );

        list($errno, $errmsg) = $this->assertADOdbError('updateClobFile()');

        $this->db->completeTrans();

        $this->assertTrue(
            $result,
            'updateClobFile should return true on success'
        );
    }

    /**
     * Test for {@see blobDecode}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:blobDecode
     *
     * @return void
     */
    public function testClobDecodeFile(): void
    {

        if ($this->skipFollowingTests) {
            return;
        }

        $newFileArray = explode('.', $this->testClobFile);
        $extension = array_pop($newFileArray);
        $newFile = implode('.', $newFileArray) . '-file-decoded.' . $extension;

        $SQL = "SELECT varchar_field 
                  FROM {$this->testTableName} 
                 WHERE integer_field=9103";

        $clobSelect = $this->db->getOne($SQL);

        list($errno, $errmsg) = $this->assertADOdbError($SQL);

         $clob = $this->db->blobDecode($clobSelect, $maxsize = false, $hastrans = true, $blobtype = 'CLOB,OID');

        list($errno, $errmsg) = $this->assertADOdbError('clobDecode()');

        file_put_contents(
            $newFile,
            $clob
        );

        $this->assertFileExists(
            $newFile,
            'The clob file should have been written to ' . $newFile
        );

        /*
        * Do some filesystem checks
        */
        $originalFileSize = filesize($this->testClobFile);
        $decodedFileSize  = filesize($newFile);


        $this->assertSame(
            $originalFileSize,
            $decodedFileSize,
            'Clob Decoded file size after Clob Encode File should match the original file size'
        );
    }
}
