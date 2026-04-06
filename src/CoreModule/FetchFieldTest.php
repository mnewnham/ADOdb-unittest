<?php

/**
 * Tests cases for FieldCount of ADODb
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

use MNewnham\ADOdbUnitTest\CoreModule\ADOdbCoreSetup;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class FieldCount
 *
 * Test cases for for ADOdb recordCount
 */
class FetchFieldTest extends ADOdbCoreSetup
{
    protected $tt1Fields = [
        'id',
        'varchar_field',
        'datetime_field',
        'date_field',
        'integer_field',
        'decimal_field',
        'boolean_field',
        'empty_field',
        'number_run_field'
    ];

    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        $db        = $GLOBALS['ADOdbConnection'];
    }

    /**
     * Test fetchField against an unbound statement
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testFetchFieldWithoutBindUsingInvalidId(
        int $fetchMode,
        string $fetchDescription
    ): void {


        $this->insertFetchMode($fetchMode);

        $sql = "SELECT id FROM testtable_3 ORDER BY id DESC";
        $lastId = $this->db->getOne($sql);

        $SQL = "SELECT * FROM testtable_3 WHERE id=-1";
        $result = $this->db->execute($SQL);

        $this->assertEquals(
            9,
            $result->fieldCount(),
            sprintf('[FETCH %s] FieldCount should return 9 with no bind usage and invalid id', $fetchDescription)
        );

        for ($i = 0; $i < $result->fieldCount(); $i++) {
            $fieldObject = $result->fetchField($i);

            $this->assertIsObject(
                $fieldObject,
                sprintf(
                    '[FETCH %s] Fetch of field %s with no bind usage of invalid id should return an object',
                    $fetchDescription,
                    $i
                )
            );

            $this->assertInstanceOf(
                'ADOFieldObject',
                $fieldObject,
                sprintf(
                    '[FETCH %s] Fetch of field %s with no bind usage of invalid id should return an ADOField object',
                    $fetchDescription,
                    $i
                )
            );

            $this->assertEquals(
                strtoupper($fieldObject->name),
                strtoupper($this->tt1Fields[$i]),
                sprintf(
                    '[FETCH %s] Expected field name with no bind usage and invalid id %s at position %d, found %s',
                    $fetchDescription,
                    $this->tt1Fields[$i],
                    $i,
                    $fieldObject->name,
                )
            );
        }
    }

    /**
     * Test fetchField against a bound statement
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testFetchFieldWithBindUsingInvalidId(
        int $fetchMode,
        string $fetchDescription
    ): void {

        $this->insertFetchMode($fetchMode);

        $p1 = $this->db->param('p1');

        $bind = ['p1' => -1];

        $SQL = "SELECT * FROM testtable_3 WHERE id=$p1";
        $result = $this->db->execute($SQL, $bind);

        $this->assertEquals(
            9,
            $result->fieldCount(),
            sprintf('[FETCH %s] FieldCount should return 9 with bind usage and invalid id', $fetchDescription)
        );

        for ($i = 0; $i < $result->fieldCount(); $i++) {
            $fieldObject = $result->fetchField($i);

            $this->assertIsObject(
                $fieldObject,
                sprintf(
                    '[FETCH %s] Fetch of field %s with bind usage of invalid id should return an object',
                    $fetchDescription,
                    $i
                )
            );

            $this->assertInstanceOf(
                'ADOFieldObject',
                $fieldObject,
                sprintf(
                    '[FETCH %s] Fetch of field %s with bind usage of invalid id should return an ADOField object',
                    $fetchDescription,
                    $i
                )
            );

            $this->assertEquals(
                strtoupper($fieldObject->name),
                strtoupper($this->tt1Fields[$i]),
                sprintf(
                    '[FETCH %s] Expected field name with bind usage and invalid id %s at position %d, found %s',
                    $fetchDescription,
                    $this->tt1Fields[$i],
                    $i,
                    $fieldObject->name,
                )
            );
        }
    }


    /**
     * Test fetchField against an unbound statement
     *
     * @return void
     */
    #[DataProvider('globalProviderFetchModes')]
    public function testFetchFieldWithoutBindUsingValidId(
        int $fetchMode,
        string $fetchDescription
    ): void {


        $this->insertFetchMode($fetchMode);

        $sql = "SELECT id FROM testtable_3 ORDER BY id DESC";
        $lastId = $this->db->getOne($sql);

        if (!$lastId) {
            
            $this->fail(
                'Could not find valid record from testtable_3'
            );

            return;

        }
        $SQL = "SELECT * FROM testtable_3 WHERE id=$lastId";
        $result = $this->db->execute($SQL);

        $this->assertEquals(
            9,
            $result->fieldCount(),
            sprintf('[FETCH %s] FieldCount should return 9 with no bind usage and valid id', $fetchDescription)
        );

        for ($i = 0; $i < $result->fieldCount(); $i++) {
            $fieldObject = $result->fetchField($i);

            $this->assertIsObject(
                $fieldObject,
                sprintf(
                    '[FETCH %s] Fetch of field %s with no bind usage of valid id should return an object',
                    $fetchDescription,
                    $i
                )
            );

            $this->assertInstanceOf(
                'ADOFieldObject',
                $fieldObject,
                sprintf(
                    '[FETCH %s] Fetch of field %s with no bind usage of valid id should return an ADOField object',
                    $fetchDescription,
                    $i
                )
            );

            $this->assertEquals(
                $fieldObject->name,
                $this->tt1Fields[$i],
                sprintf(
                    '[FETCH %s] Expected field name with no bind usage and valid id %s at position %d, found %s',
                    $fetchDescription,
                    $this->tt1Fields[$i],
                    $i,
                    $fieldObject->name,
                )
            );
        }
    }

    /**
     * Test fetchField against a bound statement
     *
     * @return void
     */
     #[DataProvider('globalProviderFetchModes')]
    public function testFetchFieldWithBindUsingValidId(
        int $fetchMode,
        string $fetchDescription
    ): void {

         $this->insertFetchMode($fetchMode);

         $sql = "SELECT id FROM testtable_3 ORDER BY id DESC";
         $lastId = $this->db->getOne($sql);

         $p1 = $this->db->param('p1');

         $bind = ['p1' => $lastId];

         $SQL = "SELECT * FROM testtable_3 WHERE id=$p1";
         $result = $this->db->execute($SQL, $bind);

         $this->assertEquals(
             9,
             $result->fieldCount(),
             sprintf('[FETCH %s] FieldCount should return 9 with bind usage and valid id', $fetchDescription)
         );

         for ($i = 0; $i < $result->fieldCount(); $i++) {
             $fieldObject = $result->fetchField($i);

             $this->assertIsObject(
                 $fieldObject,
                 sprintf(
                     '[FETCH %s] Fetch of field %s with bind usage of valid id should return an object',
                     $fetchDescription,
                     $i
                 )
             );

             $this->assertInstanceOf(
                 'ADOFieldObject',
                 $fieldObject,
                 sprintf(
                     '[FETCH %s] Fetch of field %s with bind usage of valid id should return an ADOField object',
                     $fetchDescription,
                     $i
                 )
             );

             $this->assertEquals(
                 $fieldObject->name,
                 $this->tt1Fields[$i],
                 sprintf(
                     '[FETCH %s] Expected field name with bind usage and valid id %s at position %d, found %s',
                     $fetchDescription,
                     $this->tt1Fields[$i],
                     $i,
                     $fieldObject->name,
                 )
             );
         }
    }
}
