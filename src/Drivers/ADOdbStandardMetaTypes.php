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
    const I3_MAX = 8388607;
    const I4_MAX  = 2147483647;
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
                "field_%d_%s %s",
                $key,
                str_replace(' ', '', strtolower($data['db'])),
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
     * @param string $baseFieldName
     * @param mixed  $fieldType
     * @param int    $fieldOffset
     * @param object $fetchFieldObject
     *
     * @return void
     */
    #[DataProvider('providerTestDriverSpecificMetaTypes')]
    public function testDriverSpecificMetaTypesAgainstDataDict(
        string $baseFieldName,
        mixed $fieldType,
        int $fieldOffset,
        object $fetchFieldObject,
        object $metaColumnObject
    ): void {

        if (!$baseFieldName) {
            $this->markTestSkipped('metatype_test table not found for driver');
            return;
        }

        $name     = $fetchFieldObject->name;

        /*
        * converts the dynamically created field name, e.g. field_24
        * into the array offset to lookup e.g. 24
        */

        $fieldArray = explode('_', $name);

        $nameData = $this->databaseFieldsDefinition[$fieldArray[1]];

        $expectedActualType     = $nameData['output'];
        $expectedFetchFieldType = $nameData['ff'];
        $expectedMetaType       = $nameData['meta'];
        $driverColType          = $nameData['db'];

        if (strcasecmp($expectedActualType, 'typex') == 0) {
            $expectedActualType =  $GLOBALS['ADOdataDictionary']->typeX;
        } elseif (strcasecmp($expectedActualType, 'typexl') == 0) {
            $expectedActualType =  $GLOBALS['ADOdataDictionary']->typeXL;
        }

        /*
        * Stage 1, pass a fieldobject to MetaType() as first arg
        */
        $ffResult = $GLOBALS['ADOdataDictionary']->metaType($fetchFieldObject);

        $this->assertSame(
            $expectedFetchFieldType,
            $ffResult,
            sprintf(
                'Checking MetaType of field [%s] derived from DB type [%s] using fetchField() returned' .
                ' %s, should have returned %s',
                $name,
                $driverColType,
                $ffResult,
                $expectedFetchFieldType
            )
        );

        $metaResult = $GLOBALS['ADOdataDictionary']->metaType($metaColumnObject);

        $this->assertSame(
            $expectedMetaType,
            $metaResult,
            sprintf(
                'Checking MetaType of field [%s] derived from DB type [%s] using metaColumns() returned' .
                ' %s, should have returned %s',
                $name,
                $driverColType,
                $metaResult,
                $expectedMetaType
            )
        );


        
        $actualResult = $GLOBALS['ADOdataDictionary']->actualType($metaResult);

        $this->assertSame(
            $expectedActualType,
            $actualResult,
            sprintf(
                'Checking ActualType of field [%s] derived from DB ' .
                'type [%s] using MetaType [%s] returned' .
                ' by MetaType passing fieldObject as 1st parameter',
                $name,
                $driverColType,
                $expectedMetaType
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

        $metaColumns = $GLOBALS['ADOdbConnection']->metaColumns('metatype_test');

        $sql = 'SELECT * FROM metatype_test';
        $executionResult = $GLOBALS['ADOdbConnection']->execute($sql);

        $cols = $executionResult->fieldCount();

        $returnData = [];
        for ($i = 1; $i < $cols; $i++) {
            $fetchFieldObject = $executionResult->fetchField($i);
            $metaColumnObject = $metaColumns[strtoupper($fetchFieldObject->name)];

            $returnData[$fetchFieldObject->name] = array(
                $fetchFieldObject->name,
                $fetchFieldObject->type,
                $i,
                $fetchFieldObject,
                $metaColumnObject
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
                $fieldName = sprintf(
                    "field_%d_%s",
                    $index,
                    str_replace(' ', '', strtolower($columnData['db']))
                );
                $fields[$fieldName] = self::I1_MAX - 1;
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
        $this->db->completeTrans();

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
                $fieldName = sprintf(
                    "field_%d_%s",
                    $index,
                    str_replace(' ', '', strtolower($columnData['db']))
                );
                $fields[$fieldName] = self::I2_MAX - 1;
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
        $this->db->completeTrans();

        $this->assertIsObject(
            $result,
            'A Maximum value I2 Integer value should have been inserted'
        );
    }

    /**
     * Checks that a maximum I3 value can be inserted
     *
     *
     * @return void
     */
    public function testI3ValueInsertions(): void
    {

        $fields = [];

        foreach ($this->databaseFieldsDefinition as $index => $columnData) {
            if ($columnData['meta'] == 'I3') {
                $fieldName = sprintf(
                    "field_%d_%s",
                    $index,
                    str_replace(' ', '', strtolower($columnData['db']))
                );
                $fields[$fieldName] = self::I4_MAX - 1;
            }
        }

        if (count($fields) == 0) {
            $this->markTestSkipped(
                'No I4 columns in database for test insertion'
            );
            return;
        }

        $template = $this->db->execute('SELECT * FROM metatype_test WHERE id=-1');

        $sql = $this->db->getInsertSql($template, $fields);

        $this->db->startTrans();
        $result = $this->db->execute($sql);
        $this->db->completeTrans();

        $this->assertIsObject(
            $result,
            'A Maximum value I4 Integer value should have been inserted'
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
                $fieldName = sprintf(
                    "field_%d_%s",
                    $index,
                    str_replace(' ', '', strtolower($columnData['db']))
                );
                $fields[$fieldName] = self::I4_MAX - 1;
            }
        }

        if (count($fields) == 0) {
            $this->markTestSkipped(
                'No I4 columns in database for test insertion'
            );
            return;
        }

        $template = $this->db->execute('SELECT * FROM metatype_test WHERE id=-1');

        $sql = $this->db->getInsertSql($template, $fields);

        $this->db->startTrans();
        $result = $this->db->execute($sql);
        $this->db->completeTrans();

        $this->assertIsObject(
            $result,
            'A Maximum value I4 Integer value should have been inserted'
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
                $fieldName = sprintf(
                    "field_%d_%s",
                    $index,
                    str_replace(' ', '', strtolower($columnData['db']))
                );
                $fields[$fieldName] = self::I8_MAX - 1;
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
        $this->db->completeTrans();

        $this->assertIsObject(
            $result,
            'A Maximum value I8 Integer value should have been inserted'
        );
    }
}
