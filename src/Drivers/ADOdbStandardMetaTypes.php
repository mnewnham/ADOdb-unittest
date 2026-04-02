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

namespace MNewnham\ADOdbUnitTest\Drivers;

use MNewnham\ADOdbUnitTest\Meta\MetaFunctions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaTypesTest
 *
 * Test cases for for ADOdb MetaTypes
 */
class ADOdbStandardMetaTypes extends MetaFunctions
{
    /**
     * Constants defining the maximum acceptable
     * calues for signed variables of each integer
     * type
     */
    const I1_MAX = 127;
    const I2_MAX = 32767;
    const I4_MAX = 8388607;
    const I_MAX  = 2147483647;
    const I8_MAX = 9223372036854775807;

    /**
    * db     - The native database data type. There should be one for every supported native tyoe
    * meta   - The ADOdb metatype that should be returned by metaType()
    * output - The value returned by actualType() when the metaType is passed()
    * build  - A column definition to pass in to the table building function fot testing
    *
    * @var array
    */
    public array $databaseFieldsDefinition = [
        [
            'db' => '',
            'meta' => '',
            'output' => '',
            'build' => ''
        ]
    ];


    /**
     * A database specific create table statement that wraps the
     * build statements above
     *
     * @var string
     */
    public string $createTableWrapper = '
    CREATE TABLE metatype_test(
    %s
    );';

    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();

         $columnTypesFile = sprintf(
             '%s/DriverControl/%s/ColumnTypes.inc',
             $GLOBALS['unitTestToolsDirectory'],
             $GLOBALS['SqlProvider']
         );

        if (!file_exists($columnTypesFile)) {
            return;
        }

        require_once $columnTypesFile;

        $columnTypes = new \columnTypes();

        $createTableWrapper = $columnTypes->createTableWrapper;
        $buildArray = $columnTypes->databaseFieldsDefinition;

        $columnStrings = [];
        foreach ($buildArray as $key => $data) {
            if (!$data['build']) {
                /*
                * Reverse test only
                */
                continue;
            }

            $columnStrings[] = sprintf(
                "
            field_%d %s",
                $key,
                $data['build']
            );
        }

        $columnString = implode(',', $columnStrings);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $GLOBALS['ADOdbConnection']->startTrans();
        }
        $GLOBALS['ADOdbConnection']->execute('DROP TABLE IF EXISTS metatype_test');

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $GLOBALS['ADOdbConnection']->completeTrans();
        }

        $sql = sprintf($createTableWrapper, $columnString);

        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $GLOBALS['ADOdbConnection']->startTrans();
        }

        $GLOBALS['ADOdbConnection']->execute($sql);
        if ($GLOBALS['DriverControl']->dictionaryRequireTransactions) {
            $GLOBALS['ADOdbConnection']->completeTrans();
        }
    }

    public function setup(): void
    {
        parent::setup();

        $columnTypesFile = sprintf(
            '%s/DriverControl/%s/ColumnTypes.inc',
            $GLOBALS['unitTestToolsDirectory'],
            $GLOBALS['SqlProvider']
        );

        if (!file_exists($columnTypesFile)) {
            return;
        }

        require_once $columnTypesFile;

        $columnTypes = new \columnTypes();

        $this->databaseFieldsDefinition = $columnTypes->databaseFieldsDefinition;
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
        mixed $fieldType,
        int $fieldOffset,
        object $metaFetch
    ): void {


        if (!$baseFieldName) {
            $this->markTestSkipped('metatype_test table not found for driver');
            return;
        }
        $name     = $metaFetch->name;
        $fieldArray = explode('_', $name);
        $nameData = $this->databaseFieldsDefinition[$fieldArray[1]];



        $expectedActualType    = $nameData['output'];
        //$expectedSize          = $nameData[1];
        $expectedMetaType      = $nameData['meta'];
        $driverColType         = $nameData['db'];


        if (strcasecmp($expectedMetaType, 'typex') == 0) {
            $expectedMetaType =  $GLOBALS['ADOdataDictionary']->typeX;
        } elseif (strcasecmp($expectedMetaType, 'typexl') == 0) {
            $expectedMetaType =  $GLOBALS['ADOdataDictionary']->typeXL;
        }
        //if ($expectedSize) {
        //    $expectedActualType .= sprintf('(%s)', $expectedSize);
        //}

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
                'Checking MetaType of field [%s] derived from DB type [%s] returned' .
                ' by Metatype passing fieldObject as 1st parameter',
                $name,
                $driverColType
            )
        );

        $actualResult = $GLOBALS['ADOdataDictionary']->actualType($metaResult);

        $this->assertSame(
            $expectedActualType,
            $actualResult,
            sprintf(
                'Checking ActualType of field [%s] derived from DB ' .
                'type [%s] using MetaType [%s] returned' .
                ' by MetaType passing fieldObject as 1st parameter
                %s',
                $name,
                $driverColType,
                $expectedMetaType,
                print_r($metaFetch, true)
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

        $tableName = $GLOBALS['ADOdbConnection']->metaTables('T', false, 'metatype_test');
        if (!$tableName) {
            return [[
                '',
                '',
                0,
                new \stdClass()
            ]];
        }
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

    /**
     * Checks that a maximum I1 value can be inserted into the database
     *
     * @return void
     */
    public function testI1ValueInsertions(): void
    {

        $fields = [];

        foreach ($this->databaseFieldsDefinition as $index => $columnData) {
            if ($columnData['meta'] == 'I1') {
                $fields['field_' . $index] = self::I1_MAX - 1;
            }
        }

        if (count($fields) == 0) {
            $this->markTestSkipped(
                'No I1 columns in database for test insertion'
            );
            return;
        }

        $template = $this->db->execute('SELECT * FROM metatype_test WHERE id=-1');


        $sql = $this->db->getInsertSql($template, $fields);

        $this->db->startTrans();
        $result = $this->db->execute($sql);
        $this->db->completeTrans(false);

        $this->assertIsObject(
            $result,
            'A Maximum value I1 Integer value should have been inserted'
        );
    }

    /**
     * Checks that a maximum I2 value can be inserted
     *
     * @return void
     */
    public function testI2ValueInsertions(): void
    {

        $fields = [];

        foreach ($this->databaseFieldsDefinition as $index => $columnData) {
            if ($columnData['meta'] == 'I2') {
                $fields['field_' . $index] = self::I2_MAX - 1;
            }
        }

        if (count($fields) == 0) {
            $this->markTestSkipped(
                'No I2 columns in database for test insertion'
            );
            return;
        }

        $template = $this->db->execute('SELECT * FROM metatype_test WHERE id=-1');

        $sql = $this->db->getInsertSql($template, $fields);

        $this->db->startTrans();
        $result = $this->db->execute($sql);
        $this->db->completeTrans(false);

        $this->assertIsObject(
            $result,
            'A Maximum value I2 Integer value should have been inserted'
        );
    }

    /**
     * Checks that a maximum I4 value can be inserted
     *
     *
     * @return void
     */
    public function testI4ValueInsertions(): void
    {

        $fields = [];

        foreach ($this->databaseFieldsDefinition as $index => $columnData) {
            if ($columnData['meta'] == 'I4') {
                $fields['field_' . $index] = self::I4_MAX - 1;
            }
        }

        if (count($fields) == 0) {
            $this->markTestSkipped(
                'No I2 columns in database for test insertion'
            );
            return;
        }

        $template = $this->db->execute('SELECT * FROM metatype_test WHERE id=-1');

        $sql = $this->db->getInsertSql($template, $fields);

        $this->db->startTrans();
        $result = $this->db->execute($sql);
        $this->db->completeTrans(false);

        $this->assertIsObject(
            $result,
            'A Maximum value I4 Integer value should have been inserted'
        );
    }

    /**
     * Checks that a maximum I value can be inserted
     *
     *
     * @return void
     */
    public function testIValueInsertions(): void
    {

        $fields = [];

        foreach ($this->databaseFieldsDefinition as $index => $columnData) {
            if ($columnData['meta'] == 'I') {
                $fields['field_' . $index] = self::I_MAX - 1;
            }
        }

        if (count($fields) == 0) {
            $this->markTestSkipped(
                'No I columns in database for test insertion'
            );
            return;
        }

        $template = $this->db->execute('SELECT * FROM metatype_test WHERE id=-1');

        $sql = $this->db->getInsertSql($template, $fields);

        $this->db->startTrans();
        $result = $this->db->execute($sql);
        $this->db->completeTrans(false);

        $this->assertIsObject(
            $result,
            'A Maximum value I Integer value should have been inserted'
        );
    }

    /**
     * Checks that a maximum I4 value can be inserted
     *
     *
     * @return void
     */
    public function testI8ValueInsertions(): void
    {

        $fields = [];

        foreach ($this->databaseFieldsDefinition as $index => $columnData) {
            if ($columnData['meta'] == 'I8') {
                $fields['field_' . $index] = self::I8_MAX - 1;
            }
        }

        if (count($fields) == 0) {
            $this->markTestSkipped(
                'No I8 columns in database for test insertion'
            );
            return;
        }

        $template = $this->db->execute('SELECT * FROM metatype_test WHERE id=-1');

        $sql = $this->db->getInsertSql($template, $fields);

        $this->db->startTrans();
        $result = $this->db->execute($sql);
        $this->db->completeTrans(false);

        $this->assertIsObject(
            $result,
            'A Maximum value I8 Integer value should have been inserted'
        );
    }
}
