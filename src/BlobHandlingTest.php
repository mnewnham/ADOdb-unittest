<?php

/**
 * Tests cases for BLOB I/O handling, such as images
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
 * @link https://github.com/mnewnham/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */
use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class BlobHandlingTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class BlobHandlingTest extends ADOdbTestCase
{
    protected ?string $testBlobFile;


    protected string $testTableName = 'testtable_2';


    protected int $integerField = 9002;

    /**
     * Set up the test environment
     *
     * This method is called once before any tests are run.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {


        if (!array_key_exists('testBlob', $GLOBALS['TestingControl']['blob'])) {
            return;
        }

        $testBlobFile = $GLOBALS['TestingControl']['blob']['testBlob'];
        if (!$testBlobFile) {
            return;
        }
        if (!file_exists($testBlobFile)) {
            return;
        }

        $GLOBALS['ADOdbConnection']->startTrans();

        /*
        * Find the id that matches integer_field in testtable_1
        * We will use this to set up a foreign key in testtable_2
        */
        $fksql = "(SELECT id 
                  FROM testtable_1 
                 WHERE integer_field=9002)";

        $sql = "INSERT INTO testtable_2 (integer_field, date_field,blob_field,tt_id)
                     VALUES (9002,'2025-02-01',null,$fksql)";


        $GLOBALS['ADOdbConnection']->Execute($sql);

        $GLOBALS['ADOdbConnection']->completeTrans();
    }


    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();

        if (!array_key_exists('testBlob', $GLOBALS['TestingControl']['blob'])) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'The testBlob setting is not defined in the adodb-unittest.ini file'
            );
        }

        $this->testBlobFile = $GLOBALS['TestingControl']['blob']['testBlob'];

        if (!$this->testBlobFile) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'Blob sets will be skipped'
            );
        }

        if (!file_exists($this->testBlobFile)) {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'The testBlob file does not exist: ' . $this->testBlobFile
            );
        }
    }


    /**
     * Test for {@see updateBlob}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:blobEncode
     *
     * @return void
     */
    public function testBlobEncode(): void
    {

        if ($this->skipFollowingTests) {
            return;
        }


        $fd   = file_get_contents($this->testBlobFile);


        $this->db->startTrans();
        $blob = $this->db->blobEncode($fd);
        list($errno, $errmsg) = $this->assertADOdbError('blobEncode()');
        $this->db->completeTrans();

        $hasData = strlen($blob) > 0;

        $this->assertSame(
            true,
            $hasData,
            'Blob encoding should not return an empty string'
        );
    }

    /**
     * Test for {@see updateBlob}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:updateblob
     *
     * @return void
     */
    public function testUpdateBlob(): void
    {



        //$this->db->debug = false; // Disable debug output for this test
        $fd = file_get_contents($this->testBlobFile);
        $blob = $this->db->blobEncode($fd);
        list($errno, $errmsg) = $this->assertADOdbError('blobEncode()');

        $this->db->startTrans();

        $result = $this->db->updateBlob(
            $this->testTableName,
            'blob_field',
            $blob,
            'integer_field=' . $this->integerField
        );

        list($errno, $errmsg) = $this->assertADOdbError('updateBlob()');

        $this->db->completeTrans();

        $this->assertTrue(
            $result,
            'updateBlob() should return true on success'
        );
    }

    /**
     * Test for {@see updateBlobFile}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:updateblobfile
     *
     * @return void
     */
    public function testUpdateBlobFile(): void
    {

        if ($this->skipFollowingTests) {
            $this->markTestSkipped(
                'Skipping testUpdateBlob as the testBlob setting ' .
                'is not defined in the adodb-unittest.ini file'
            );
            return;
        }

        $this->db->startTrans();

        $result = $this->db->updateBlobFile(
            $this->testTableName,
            'blob_field',
            $this->testBlobFile,
            'integer_field=' . $this->integerField
        );

        list($errno, $errmsg) = $this->assertADOdbError('updateBlobFile()');

        $this->db->completeTrans();

        $this->assertTrue(
            $result,
            'updateBlob should return true on success'
        );
    }


    /**
     * Test for {@see blobDecode}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:blobDecode
     *
     * @return void
     */
    public function testBlobDecode(): void
    {

        if ($this->skipFollowingTests) {
            return;
        }

        $newFileArray = explode('.', $this->testBlobFile);
        $extension = array_pop($newFileArray);
        $newFile = implode('.', $newFileArray) . '-decoded' . $extension;


        $SQL = "SELECT LENGTH(blob_field) 
                  FROM  {$this->testTableName} 
                 WHERE integer_field={$this->integerField}";

        $blobLength = $this->db->getOne($SQL);
        list($errno, $errmsg) = $this->assertADOdbError($SQL);

        $this->assertGreaterThan(
            0,
            $blobLength,
            'The blob field should contain data'
        );


        $SQL = "SELECT blob_field 
                  FROM {$this->testTableName} 
                 WHERE integer_field={$this->integerField}";


        $blobSelect = $this->db->getOne($SQL);

        list($errno, $errmsg) = $this->assertADOdbError($SQL);

        $blob = $this->db->blobDecode($blobSelect);
        list($errno, $errmsg) = $this->assertADOdbError('blobDecode()');

        file_put_contents(
            $newFile,
            $blob
        );

        $this->assertFileExists(
            $newFile,
            'The blob file should have been written to ' . $newFile
        );

        /*
        * Do some filesystem checks
        */
        $originalFileSize = filesize($this->testBlobFile);
        $decodedFileSize  = filesize($newFile);


        $this->assertSame(
            $originalFileSize,
            $decodedFileSize,
            'Blob Decoded file size should match the original file size'
        );
    }
}
