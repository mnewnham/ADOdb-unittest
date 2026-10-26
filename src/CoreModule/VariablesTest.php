<?php

/**
 * Tests cases for variables and constants of ADODb
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

namespace MNewnham\ADOdbUnitTest\CoreModule;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class VariablesTest extends ADOdbTestCase
{
    protected ?object $db;
    protected ?string $adoDriver;
    protected ?object $dataDictionary;

    protected bool $skipFollowingTests = false;

    protected string $testTableName = 'select';
    protected string $testIdColumnName = 'ID';


    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        $db = $GLOBALS['ADOdbConnection'];
        
    }

    public function setup(): void
    {
        parent::setup();
    }

    /**
     * Tests if the isConnected method works
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:isconnected
     *
     * @return void
     */
    public function testIsConnected(): void
    {
        $isConnected = $this->db->isConnected();

        $this->assertSame(
            true,
            $isConnected,
            'A connected database should return true from the isConnected() method'
        );
    }

    
    /**
     * Test for {@see $ADODB_GETONE_EOF}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_getone_eof
     *
     * @return void
     */
    public function testGetOneEof(): void
    {
        global $ADODB_GETONE_EOF;

        $sql = 'select varchar_field from testtable_1 where id=9999';
        $test = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            $test,
            false,
            'getOne by default should return false when no row is found'
        );

        $ADODB_GETONE_EOF = -1;

        $test = $this->db->getOne($sql);
        list($errno, $errmsg) = $this->assertADOdbError($sql);

        $this->assertEquals(
            $test,
            -1,
            'getOne should now flag by -1 when no row is found'
        );
    }
    /**
     * Test for {@see $ADODB_COUNTRECS}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_countrecs
     *
     * @return void
     */
    public function testCountRecsUsingCountRecsFalse(): void
    {
        global $ADODB_COUNTRECS;
        $ADODB_COUNTRECS = false; // Set to true by default

        $sql = 'SELECT varchar_field FROM testtable_1 WHERE id<9999';

        list($result, $errno, $errmsg) = $this->executeSqlString($sql);

        $this->assertEquals(
            -1,
            $result->recordCount(),
            'With ADODB_COUNTRECS set to false, the record count should be -1'
        );

        $ADODB_COUNTRECS = true; // Reset to default for other tests
    }

    /**
     * Test for {@see $ADODB_COUNTRECS}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:adodb_countrecs
     *
     * @return void
     */
    public function testCountRecsUsingCountRecsTrue(): void
    {
        global $ADODB_COUNTRECS;
        $ADODB_COUNTRECS = true; // Set to true by default

        $sql = "SELECT COUNT(*) FROM testtable_3";

        $this->db->startTrans();
        $countedRecords = $this->db->getOne($sql);
        $this->db->completeTrans();


        $sql = "SELECT * FROM testtable_3";

        list($result, $errno, $errmsg) = $this->executeSqlString($sql);

        $this->assertEquals(
            $countedRecords,
            $result->recordCount(),
            'With ADODB_COUNTRECS set to true, the record count should be ' . $countedRecords
        );

        $result->fetchRow();
    }

    /**
     * Tests the charMax() method
     *
     * @return void
     */
    public function testCharMax(): void
    {

        $value = $this->db->charMax();

        $this->assertIsInt(
            $value,
            'charMax() should return an integer value'
        );
    }

    /**
     * Tests the textMax() method
     *
     * @return void
     */
    public function testTextMax(): void
    {

        $value = $this->db->textMax();

        $this->assertIsInt(
            $value,
            'textMax() should return an integer value'
        );
    }
}
