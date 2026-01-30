<?php

/**
 * Tests cases for MetaTypes functions of ADODb
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
 * Class MetaTypesTest
 *
 * Test cases for for ADOdb MetaTypes
 */
class MetaTypesTest extends MetaFunctions
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
     * Test for {@see ADODConnection::metaType()]
     * Checks that the correct metatype is returned
     *
     * @param ?string $metaType
     * @param int $fieldLength
     * @param int $offset
     * @param string $actualResult
     *
     * @return void
     */
    #[DataProvider('providerTestMetaTypes')]
    public function testMetaTypesAgainstDataDict(
        mixed $metaType,
        int $fieldLength,
        int $offset,
        string $actualResult
    ): void {



        $sql = 'SELECT * FROM ' . $this->testTableName;
        list ($executionResult, $errno, $errmsg) = $this->executeSqlString($sql);

        $metaResult = false;
        $metaFetch = $executionResult->fetchField($offset);


        if ($metaFetch != false) {
            /*
            * Stage 1, pass a string and length to MetaType()
            */

            $metaResult = $GLOBALS['ADOdataDictionary']->metaType(
                $metaFetch->type,
                $metaFetch->max_length
            );

            $this->assertSame(
                $metaType,
                $metaResult,
                'Checking MetaType passing string type and length'
            );

            $actualType = $GLOBALS['ADOdataDictionary']->actualType($metaType);

            $this->assertSame(
                $actualType,
                $actualResult,
                'Checking ActualType returned by MetaType using string type and length'
            );

            /*
            * Stage 2, pass a fieldobject to MetaType() as first arg
            */
            $metaResult = $GLOBALS['ADOdataDictionary']->metaType($metaFetch);

            $this->assertSame(
                $metaType,
                $metaResult,
                'Checking MetaType passing field object as 1st parameter'
            );

            $actualType = $GLOBALS['ADOdataDictionary']->actualType($metaType);

            $this->assertSame(
                $actualType,
                $actualResult,
                'Checking ActualType returned by MetaType using fieldobject as 1st parameter'
            );

            /*
            * Stage 3, pass a fieldobject to MetaType() as third arg
            */
            $metaResult = $GLOBALS['ADOdataDictionary']->metaType('', -1, $metaFetch);

            $this->assertSame(
                $metaType,
                $metaResult,
                'Checking MetaType passing field object as 3rd parameter'
            );

            $actualType = $GLOBALS['ADOdataDictionary']->actualType($metaType);

            $this->assertSame(
                $actualType,
                $actualResult,
                'Checking ActualType returned by MetaType using fieldobject as 3rd parameter'
            );
        }
    }

    /**
     * Test for {@see ADODConnection::metaType()]
     * Checks that the correct metatype is returned
     *
     * @param ?string $metaType
     * @param int $fieldLength
     * @param int $offset
     * @param string $actualResult
     *
     * @return void
     */
    #[DataProvider('providerTestMetaTypes')]
    public function testMetaTypesAgainstAdoConnection(
        mixed $metaType,
        int $fieldLength,
        int $offset,
        string $actualResult
    ): void {



        $sql = 'SELECT * FROM ' . $this->testTableName;
        list ($executionResult, $errno, $errmsg) = $this->executeSqlString($sql);

        $metaResult = false;
        $metaFetch = $executionResult->fetchField($offset);


        if ($metaFetch != false) {
            /*
            * Stage 1, pass a string and length to MetaType()
            */

            $metaResult = $this->db->metaType(
                $metaFetch->type,
                $metaFetch->max_length
            );

            $this->assertSame(
                $metaType,
                $metaResult,
                'Checking MetaType passing string type and length'
            );

            $actualType = $GLOBALS['ADOdataDictionary']->actualType($metaType);

            $this->assertSame(
                $actualType,
                $actualResult,
                'Checking ActualType returned by MetaType using string type and length'
            );

            /*
            * Stage 2, pass a fieldobject to MetaType() as first arg
            */
            $metaResult = $this->db->metaType($metaFetch);

            $this->assertSame(
                $metaType,
                $metaResult,
                'Checking MetaType passing field object as 1st parameter'
            );

            $actualType = $GLOBALS['ADOdataDictionary']->actualType($metaType);

            $this->assertSame(
                $actualType,
                $actualResult,
                'Checking ActualType returned by MetaType using fieldobject as 1st parameter'
            );

            /*
            * Stage 3, pass a fieldobject to MetaType() as third arg
            */
            $metaResult = $this->db->metaType('', -1, $metaFetch);

            $this->assertSame(
                $metaType,
                $metaResult,
                'Checking MetaType passing field object as 3rd parameter'
            );

            $actualType = $GLOBALS['ADOdataDictionary']->actualType($metaType);

            $this->assertSame(
                $actualType,
                $actualResult,
                'Checking ActualType returned by MetaType using fieldobject as 3rd parameter'
            );
        }
    }

    /**
     * Data provider for {@see testMetaTypes()}
     *
     * @return array [string metatype, int offset]
     */
    public static function providerTestMetaTypes(): array
    {

        /*
        CREATE TABLE testtable_1 (
        id INT NOT NULL AUTO_INCREMENT,
        varchar_field VARCHAR(20),
        datetime_field DATETIME,
        date_field DATE,
        integer_field INT(2) DEFAULT 0,
        decimal_field decimal(12.2) DEFAULT 0,
        boolean_field BOOLEAN DEFAULT 0,
        empty_field VARCHAR(240) DEFAULT '',
        number_run_field INT(4) DEFAULT 0,
        */

        return [
            'Field 0 Is BIGINT' => ['I', 8, 0, 'BIGINT'],
            'Field 1 Is VARCHAR' => ['C', 20, 1, 'VARCHAR'],
            'Field 2 Is DATETIME' => ['T', 8, 2, 'DATETIME'],
            'Field 3 Is DATE' => ['D', 10, 3, 'DATE'],
            'Field 4 Is INT' => ['I4', 4, 4, 'INTEGER'],
            'Field 5 Is NUMBER' => ['N', 12, 5, 'NUMERIC'],
            'Field 6 Is BOOLEAN' => ['L', 1, 6, 'BOOLEAN'],
            'Field 7 Is VARCHAR' => ['C', 240, 7, 'VARCHAR'],
            'Field 8 Is BIGINT' => ['I', 4, 8, 'BIGINT'],
            'Field 9 Does not Exist' => [false, -1, 9, ADODB_DEFAULT_METATYPE],


        ];
    }
}
