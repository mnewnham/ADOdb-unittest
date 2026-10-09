<?php

/**
 * Test data definitions for the oci8 driver of ADOdb.
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

/**
 * Class columnTypes
 *
 * Metatype Test cases for for the Oracle native driver
 */
class columnTypes
{
    /**
    * db     - The native database data type. There should be one for every supported native tyoe
    * meta   - The ADOdb metatype that should be returned by metaType()
    * output - The value returned by actualType() when the metaType is passed()
    * build  - A column definition to pass in to the table building function fot testing
    *
    * @var array
    */
    public array $databaseFieldsDefinition = [
        ['db' => 'CHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR2', 'build' => 'CHAR(10)'],
        ['db' => 'VARCHAR2', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR2', 'build' => 'VARCHAR2(10)'],
        ['db' => 'NCHAR', 'meta' => 'C2', 'ff' => 'C', 'output' => 'NVARCHAR2', 'build' => 'NCHAR(10)'],
        ['db' => 'NVARCHAR2', 'meta' => 'C2', 'ff' => 'C', 'output' => 'NVARCHAR2', 'build' => 'NVARCHAR2(10)'],
        ['db' => 'NVARCHAR2', 'meta' => 'C2', 'ff' => 'C', 'output' => 'NVARCHAR2', 'build' => 'NVARCHAR2(1000)'],
        ['db' => 'TIMESTAMP', 'meta' => 'TS', 'ff' => 'TS', 'output' => 'TIMESTAMP', 'build' => 'TIMESTAMP'],
        ['db' => 'DATE', 'meta' => 'D', 'ff' => 'D', 'output' => 'DATE', 'build' => 'DATE'],
        ['db' => 'BLOB', 'meta' => 'B', 'ff' => 'B', 'output' => 'BLOB', 'build' => 'BLOB'],
        ['db' => 'CLOB', 'meta' => 'XL', 'ff' => 'XL', 'output' => 'CLOB', 'build' => 'CLOB'],
        ['db' => 'NCLOB', 'meta' => 'XL', 'ff' => 'XL', 'output' => 'CLOB', 'build' => 'NCLOB'],
        //['db' => 'BFILE', 'meta' => 'B', 'ff' => 'B', 'output' => 'BLOB', 'build' => 'BFILE'],
        ['db' => 'LONG', 'meta' => 'XL', 'ff' => 'XL', 'output' => 'CLOB', 'build' => 'LONG'],
       // ['db' => 'RAW', 'meta' => 'B', 'ff' => 'C', 'output' =>'BLOB', 'build' => 'RAW(1000)'],
       // ['db' => 'LONGRAW', 'meta' => 'B', 'ff' => 'C', 'output' =>'BLOB', 'build' => 'LONG RAW'],
        ['db' => 'NUMBER_1', 'meta' => 'L', 'ff' => 'L', 'output' => 'NUMBER(1)', 'build' => 'NUMBER(1)'],
        ['db' => 'BOOLEAN', 'meta' => 'L', 'ff' => 'L', 'output' => 'NUMBER(1)', 'build' => 'NUMBER(1)'],
        ['db' => 'NUMBER_3', 'meta' => 'I1', 'ff' => 'I', 'output' => 'NUMBER(3)', 'build' => 'NUMBER(3)'],
        ['db' => 'NUMBER_5', 'meta' => 'I2', 'ff' => 'I', 'output' => 'NUMBER(5)', 'build' => 'NUMBER(5)'],
        ['db' => 'NUMBER_10', 'meta' => 'I4', 'ff' => 'I', 'output' => 'NUMBER(10)', 'build' => 'NUMBER(10)'],
        ['db' => 'NUMBER_20', 'meta' => 'I', 'ff' => 'I', 'output' => 'NUMBER(20)', 'build' => 'NUMBER(20)'],
        ['db' => 'NUMBER_38', 'meta' => 'I8', 'ff' => 'I', 'output' => 'NUMBER(38)', 'build' => 'NUMBER(38)'],

        ['db' => 'NUMBER_16_2', 'meta' => 'N', 'ff' => 'N', 'output' => 'NUMBER', 'build' => 'NUMBER(16,2)'],

        ['db' => 'FLOAT_126', 'meta' => 'F', 'ff' => 'F', 'output' => 'FLOAT(126)', 'build' => 'FLOAT(126)'],
        ['db' => 'FLOAT_63', 'meta' => 'R', 'ff' => 'R', 'output' => 'FLOAT(63)', 'build' => 'FLOAT(63)'],

    ];

    /**
     * A database specific create table statement that wraps the
     * build statements above
     *
     * @var string
     */
    public string $createTableWrapper = '
    CREATE TABLE metatype_test(
    id number(20),
    %s
    )';
}
