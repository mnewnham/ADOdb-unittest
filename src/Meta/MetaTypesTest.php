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

use function PHPUnit\Framework\fileExists;

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


        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/metatype-test.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        if (!file_exists($tableSchema)) {
            return;
        }

        /*
        * Loads the schema based on the DB type
        */

        readSqlIntoDatabase($GLOBALS['ADOdbConnection'], $tableSchema);
    }

    public function setup(): void
    {
        parent::setup();

        $tableSchema = sprintf(
            '%s/DatabaseSetup/%s/metatype-test.sql',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        if (!file_exists($tableSchema)) {
            $this->markTestSkipped('No driver specific test file found, Use the GenericMetaTypesTest plan instead');
            return;
        }
    }

    /**
     * Test for {@see ADODDatadict::metaType()]
     * Checks that the correct metatype is returned
     *
     * @param ?string $metaType
     * @param int $fieldLength
     * @param int $offset
     * @param string $actualResult
     *
     * @return void
     */
    #[DataProvider('providerTestDriverSpecificMetaTypes')]
    public function testDriverSpecificMetaTypesAgainstDataDict(
        string $baseFieldName,
        int $fieldType,
        int $fieldOffset,
        object $metaFetch
    ): void {


        $name     = $metaFetch->name;
        $nameData = explode('_', $name);

        $expectedMetaType      = strtoupper($nameData[1]);
        $expectedActualType    = strtoupper($nameData[0]);
        $driverColType = strtoupper($nameData[2]);

        if (1 == 2) {
        /*
        * Stage 1, pass a string and length to MetaType()
        */

            $metaResult = $GLOBALS['ADOdataDictionary']->metaType(
                $metaFetch->type,
                $metaFetch->max_length
            );

            $this->assertSame(
                $expectedMetaType,
                $metaResult,
                sprintf(
                    'Checking MetaType of field [%s] passing string ' .
                    'type [%s] and length [%s] from FetchField()',
                    $name,
                    $metaFetch->type,
                    $metaFetch->max_length
                )
            );

            $actualResult = $GLOBALS['ADOdataDictionary']->actualType($metaResult);

            $this->assertSame(
                $expectedActualType,
                $actualResult,
                sprintf(
                    '
                Checking ActualType of field [%s] returned' .
                    ' by MetaType using string type and length',
                    $name
                )
            );
        }
        /*
        * Stage 2, pass a fieldobject to MetaType() as first arg
        */
        $metaResult = $GLOBALS['ADOdataDictionary']->metaType($metaFetch);

        $this->assertSame(
            $expectedMetaType,
            $metaResult,
            sprintf(
                'Checking MetaType of field [%s] returned' .
                ' by MetaType passing fieldObject as 1st parameter',
                $name
            )
        );

        $actualResult = $GLOBALS['ADOdataDictionary']->actualType($metaResult);

        $this->assertSame(
            $expectedActualType,
            $actualResult,
            sprintf(
                'Checking ActualType of field [%s] returned' .
                ' by MetaType passing fieldObject as 1st parameter',
                $name
            )
        );

        /*
        * Stage 3, pass a fieldobject to MetaType() as third arg
        */
        $metaResult = $GLOBALS['ADOdataDictionary']->metaType('', -1, $metaFetch);

        $this->assertSame(
            $expectedMetaType,
            $metaResult,
            sprintf(
                'Checking MetaType of field [%s] returned' .
                    ' by MetaType passing fieldObject as 3rd parameter',
                $name
            )
        );

        $actualResult = $GLOBALS['ADOdataDictionary']->actualType($metaResult);

        $this->assertSame(
            $expectedActualType,
            $actualResult,
            sprintf(
                'Checking ActualType of field [%s] returned' .
                ' by MetaType passing fieldObject as 3rd parameter',
                $name
            )
        );
    }

     /**
     * Data provider for {@see testMetaTypes()}
     *
     * @return array [string metatype, int offset]
     */
    public static function providerTestDriverSpecificMetaTypes(): array
    {

        $sql = 'SELECT * FROM metatype_test';
        $executionResult = $GLOBALS['ADOdbConnection']->execute($sql);

        $cols = $executionResult->fieldCount();

        $returnData = [];
        for ($i = 1; $i < $cols; $i++) {
            $field = $executionResult->fetchField($i);
            $returnData[$field->name] = array(
                $field->name,
                $field->type,
                $i,
                $field
            );
        }

        return $returnData;
    }
}
