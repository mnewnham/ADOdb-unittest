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

         $db        = $GLOBALS['ADOdbConnection'];
        $adoDriver = $GLOBALS['ADOdriver'];

        /*
        * load foreign keys test schema
        */
        $db->startTrans();

        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/foreign-keys-schema.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        /*
        * Loads the schema based on the DB type
        */

        readSqlIntoDatabase($db, $tableSchema);

        $db->completeTrans();
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
        string $expectedFirstTableKey,
        mixed $expectedFirstFieldKeys,
        mixed $expectedSecondTableKey,
        mixed $expectedSecondFieldKeys,
        bool $upperCaseKeys,
        bool $associativeKeys,
        ?string $schemaOwner
    ): void {

         global $ADODB_FETCH_MODE;
         $originalFetchMode = $ADODB_FETCH_MODE;

         $this->db->setFetchMode($fetchMode);
         $ADODB_FETCH_MODE = $fetchMode;

         $testTable1 = 'foreign_key_target_1';
         $testTable2 = 'foreign_key_source';

         $executionResult = $this->db->metaForeignKeys(
             $sourceTable,
             $schemaOwner,
             $upperCaseKeys,
             $associativeKeys
         );

         print_r($executionResult);

         $this->db->setFetchMode($originalFetchMode);

         if ($expectedFirstFieldKeys == false) {
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
                 $expectedFirstTableKey,
                 $executionResult,
                 sprintf(
                     "[FETCH MODE %s] Checking for first foreign key for $testTable1 in $testTable2",
                     $fetchMode
                 )
             );

             $fkData = $executionResult[$expectedFirstTableKey];


             $this->assertSame(
                 $expectedFirstFieldKeys,
                 $fkData,
                 sprintf(
                     '[FETCH MODE %s] Checking that the first foreign key data matches expected values',
                     $fetchMode
                 )
             );

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
                 $expectedSecondTableKey,
                 $executionResult,
                 sprintf(
                     "[FETCH MODE %s] Checking for second foreign key for $testTable1 in $testTable2",
                     $fetchMode
                 )
             );

             $fkData = $executionResult[$expectedSecondTableKey];


             $this->assertSame(
                 $expectedSecondFieldKeys,
                 $fkData,
                 sprintf(
                     '[FETCH MODE %s] Checking that the second foreign key data matches expected values',
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
                'foreign_key_source',
                'foreign_key_target_1',
                [
                   'tt_id_1' => 'id_1',
                   'integer_field' => 'integer_field_1'
                ],
                'foreign_key_target_2',
                [
                   'tt_id_2' => 'id_2',
                   'integer_field' => 'integer_field_2'
                ],
                false,
                false,
                ''
            ],
            'Force Upper Case Keys, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC,
                'foreign_key_source',
                'FOREIGN_KEY_TARGET_1',
                [
                    'TT_ID_1' => 'ID_1',
                    'INTEGER_FIELD' => 'INTEGER_FIELD_1'
                ],
                'FOREIGN_KEY_TARGET_2',
                [
                   'TT_ID_2' => 'ID_2',
                   'INTEGER_FIELD' => 'INTEGER_FIELD_2'
                ],
                true,
                false,
                ''
            ],
            'Default Behaviour, ADODB_FETCH_NUM' => [
                ADODB_FETCH_NUM,
                'foreign_key_source',
                'foreign_key_target_1',
                ['tt_id_1=id_1','integer_field=integer_field_1'],
                'foreign_key_target_2',
                ['tt_id_2=id_2', 'integer_field=integer_field_2'],
                false,
                false,
                ''
            ],
            'Force Upper Case Keys, ADODB_FETCH_NUM' => [
                ADODB_FETCH_NUM,
                'foreign_key_source',
                'FOREIGN_KEY_TARGET_1',
                ['TT_ID_1=ID_1','INTEGER_FIELD=INTEGER_FIELD_1'],
                'FOREIGN_KEY_TARGET_2',
                ['TT_ID_2=ID_2', 'INTEGER_FIELD=INTEGER_FIELD_2'],
                true,
                false,
                ''
            ],
            'Force Associative From ADODB_FETCH_NUM, Lower Case Keys' => [
                ADODB_FETCH_NUM,
                'foreign_key_source',
                'foreign_key_target_1',
                [
                    'tt_id_1' => 'id_1',
                    'integer_field' => 'integer_field_1'
                ],
                 'foreign_key_target_2',
                [
                   'tt_id_2' => 'id_2',
                   'integer_field' => 'integer_field_2'
                ],
                false,
                true,
                ''
            ],
            'Force Associative From ADODB_FETCH_NUM, Upper Case Keys' => [
                ADODB_FETCH_NUM,
                'foreign_key_source',
                'FOREIGN_KEY_TARGET_1',
                [
                    'TT_ID_1' => 'ID_1',
                    'INTEGER_FIELD' => 'INTEGER_FIELD_1'
                ],
                'FOREIGN_KEY_TARGET_2',
                [
                   'TT_ID_2' => 'ID_2',
                   'INTEGER_FIELD' => 'INTEGER_FIELD_2'
                ],
                true,
                true,
                ''
            ],
            'Default Behaviour, Passing OWNER, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC,
                'foreign_key_source',
                'foreign_key_target_1',
                [
                   'tt_id_1' => 'id_1',
                   'integer_field' => 'integer_field_1'
                ],
                'foreign_key_target_2',
                [
                   'tt_id_2' => 'id_2',
                   'integer_field' => 'integer_field_2'
                ],
                false,
                false,
                $GLOBALS['schemaOwner']
            ],
            'Default Behaviour, Passing Invalid OWNER, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC,
                'foreign_key_source',
                'foreign_key_target_1',
                false,
                false,
                false,
                false,
                false,
                'X' . $GLOBALS['schemaOwner']
            ],
            'Default Behaviour, Passing Invalid Table, ADODB_FETCH_ASSOC' => [
                ADODB_FETCH_ASSOC,
                'invalide_foreign_key_source',
                'foreign_key_target_1',
                false,
                false,
                false,
                false,
                false,
                ''
            ],
        ];
    }
}
