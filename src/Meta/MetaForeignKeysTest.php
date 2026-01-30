<?php

/**
 * Tests cases for MetaForeignKeys functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\Meta;

use MNewnham\ADOdbUnitTest\Meta\MetaFunctions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaForeignKeysTest
 *
 * Test cases for for ADOdb MetaForeignKeys
 */
class MetaForeignKeysTest extends MetaFunctions
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
    }

    /**
     * Test for {@see ADODConnection::metaForeignKeys()]
     * Checks that the correct list of foreigh keys is returned
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:metaforeignkeys
     *
     * @return void
     */
    public function testMetaForeignKeys(): void
    {

        global $ADODB_FETCH_MODE;
        $originalFetchMode = $ADODB_FETCH_MODE;



        $this->db->setFetchMode(ADODB_FETCH_ASSOC);


        $testTable1 = 'testtable_1';
        $testTable2 = 'testtable_2';


        $executionResult = $this->db->metaForeignKeys($testTable2);
        list($errno, $errmsg) = $this->assertADOdbError('metaForeignKeys()');

        $this->db->setFetchMode($originalFetchMode);

        if ($executionResult == false) {
            $this->fail('With fetch mode set to ADODB_FETCH_ASSOC, metaForeignKeys did not return any foreign keys');
            return;
        }

        $executionResult = array_change_key_case($executionResult, CASE_UPPER);

        $fkTableNames = array_flip(
            array_change_key_case(
                array_keys($executionResult),
                CASE_UPPER
            )
        );



        $fkTableExists = $this->assertArrayHasKey(
            strtoupper($testTable1),
            $fkTableNames,
            'fetch mode ADODB_FETCH_ASSOC Checking for foreign key testtable_1 in testtable_2'
        );

        if (!$fkTableExists) {
            return;
        }

        $fkData = $executionResult[strtoupper($testTable1)];

        $this->assertArrayHasKey(
            'TT_ID',
            $fkData,
            'With fetch mode ADODB_FETCH_ASSOC, Checking for foreign key field' .
            'TT_ID in testtable_2 foreign key testtable_1'
        );

        $this->assertArrayHasKey(
            'INTEGER_FIELD',
            $fkData,
            'With fetch mode ADODB_FETCH_ASSOC, Checking for foreign key field' .
            'INTEGER_FIELD in testtable_2 foreign key testtable_1'
        );


        $this->db->setFetchMode(ADODB_FETCH_NUM);

        $executionResult = $this->db->metaForeignKeys($testTable2);
        list($errno, $errmsg) = $this->assertADOdbError('metaForeignKeys()');

        $this->db->setFetchMode($originalFetchMode);

        if ($executionResult == false) {
            $this->fail(
                'With fetch mode set to ADODB_FETCH_NUM, ' .
                'metaForeignKeys did not return any foreign keys'
            );
            return;
        }

        $executionResult = array_change_key_case($executionResult, CASE_UPPER);

        $fkTableNames = array_flip(
            array_change_key_case(
                array_keys($executionResult),
                CASE_UPPER
            )
        );


        $this->assertContains(
            'TT_ID=ID',
            $fkData,
            'With fetch mode ADODB_FETCH_NUM, Checking for foreign key ' .
            'field TT_ID in testtable_2 foreign key testtable_1'
        );

        $this->assertContains(
            'INTEGER_FIELD=INTEGER_FIELD',
            $fkData,
            'With fetch mode ADODB_FETCH_NUM Checking for foreign key ' .
            'field INTEGER_FIELD in testtable_2 foreign key testtable_1'
        );
    }

    /**
     * Test for errors when a metaforeignkeys function is called on an invalid table
     *
     * @return void
     */
    public function testMetaForeignKeysForInvalidTable(): void
    {


        foreach ($this->testFetchModes as $fetchMode => $fetchModeName) {
            $this->db->setFetchMode($fetchMode);

            $response = $this->db->metaForeignKeys('invalid_table');

            $this->assertFalse(
                $response,
                sprintf(
                    '[FETCH MODE %s] Checking that metaForeignKeys ' .
                    'returns false for an invalid table',
                    $fetchModeName
                )
            );
        }
    }
}
