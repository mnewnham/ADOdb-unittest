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
 * @copyright 2025,2026 Mark Newnham
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
     #[DataProvider('providerTestMetaForeignKeys')]
    public function testMetaForeignKeys(
        int $fetchMode,
        string $sourceTable,
        string $expectedTableKey,
        mixed $expectedFieldKeys,
        bool $upperCaseKeys,
        bool $associativeKeys,
        string $schemaOwner
    ): void {

         global $ADODB_FETCH_MODE;
         $originalFetchMode = $ADODB_FETCH_MODE;

         $this->db->setFetchMode($fetchMode);
         $ADODB_FETCH_MODE = $fetchMode;

         $testTable1 = 'testtable_1';
         $testTable2 = 'testtable_2';

         $executionResult = $this->db->metaForeignKeys(
             $sourceTable,
             $schemaOwner,
             $upperCaseKeys,
             $associativeKeys
         );

         $this->db->setFetchMode($originalFetchMode);

         if ($expectedFieldKeys == false) {
             $this->assertFalse(
                 $executionResult,
                 sprintf(
                     '[FETCH MODE %s] Checking that metaForeignKeys returns ' .
                     'false when invalid owner or table is passed',
                     $fetchMode
                 )
             );
         } else {
             if ($executionResult == false) {
                 $this->fail(
                     sprintf(
                         '[FETCH MODE %s] metaForeignKeys did not return any foreign keys',
                         $fetchMode
                     )
                 );
                 return;
             }

             $this->assertArrayHasKey(
                 $expectedTableKey,
                 $executionResult,
                 sprintf(
                     "[FETCH MODE %s] Checking for foreign key for $testTable1 in $testTable2",
                     $fetchMode
                 )
             );

             $fkData = $executionResult[$expectedTableKey];


             $this->assertSame(
                 $expectedFieldKeys,
                 $fkData,
                 sprintf(
                     '[FETCH MODE %s] Checking that the foreign key data matches expected values',
                     $fetchMode
                 )
             );
         }
    }

    /**
     * Data provider for {@see metaForeignKeys()}
     *
     * @return array [string(getRe, array return value]
     */
    public static function providerTestMetaForeignKeys(): array
    {

        return [
            'Default Behaviour, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC,
                'testtable_2',
                'testtable_1',
                [
                   'tt_id' => 'id',
                   'integer_field' => 'integer_field'
                ],
                false,
                false,
                ''
            ],
            'Force Upper Case Keys, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC,
                'testtable_2',
                'TESTTABLE_1',
                [
                    'TT_ID' => 'ID',
                    'INTEGER_FIELD' => 'INTEGER_FIELD'
                ],
                true,
                false,
                ''
            ],
            'Default Behaviour, ADODB_FETCH_NUM' => [
                ADODB_FETCH_NUM,
                'testtable_2',
                'testtable_1',
                ['tt_id=id','integer_field=integer_field'],
                false,
                false,
                ''
            ],
            'Force Upper Case Keys, ADODB_FETCH_NUM' => [
                ADODB_FETCH_NUM,
                'testtable_2',
                'TESTTABLE_1',
                ['TT_ID=ID','INTEGER_FIELD=INTEGER_FIELD'],
                true,
                false,
                ''
            ],
            'Force Associative From ADODB_FETCH_NUM, Lower Case Keys' => [
                ADODB_FETCH_NUM,
                'testtable_2',
                'testtable_1',
                [
                    'tt_id' => 'id',
                    'integer_field' => 'integer_field'
                ],
                false,
                true,
                ''
            ],
            'Force Associative From ADODB_FETCH_NUM, Upper Case Keys' => [
                ADODB_FETCH_NUM,
                'testtable_2',
                'TESTTABLE_1',
                [
                    'TT_ID' => 'ID',
                    'INTEGER_FIELD' => 'INTEGER_FIELD'
                ],
                true,
                true,
                ''
            ],
            'Default Behaviour, Passing OWNER, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC,
                'testtable_2',
                'testtable_1',
                [
                   'tt_id' => 'id',
                   'integer_field' => 'integer_field'
                ],
                false,
                false,
                $GLOBALS['schemaOwner']
            ],
            'Default Behaviour, Passing Invalid OWNER, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC,
                'testtable_2',
                'testtable_1',
                false,
                false,
                false,
                'X' . $GLOBALS['schemaOwner']
            ],
            'Default Behaviour, Passing Invalid Table, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC,
                'invalide_testtable_2',
                'testtable_1',
                false,
                false,
                false,
                ''
            ],
        ];
    }
}
