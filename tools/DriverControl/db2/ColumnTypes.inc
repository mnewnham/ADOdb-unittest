<?php

/**
 * Test data definitions for the db2 driver of ADOdb.
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
 * Metatype Test cases for for the IBM DB2 native driver
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
        ['db' => 'CHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR', 'build' => 'CHAR(10)'],              //0
        ['db' => 'VARCHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR', 'build' => 'VARCHAR(10)'],        //1
        ['db' => 'VARCHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR', 'build' => 'VARCHAR(1)'],        //2
        ['db' => 'VARGRAPHIC', 'meta' => 'C2', 'ff' => 'C', 'output' => 'VARGRAPHIC', 'build' => 'VARGRAPHIC(60)'],         //3
        ['db' => 'VARBINARY_30', 'meta' => 'C2', 'ff' => 'C', 'output' => 'VARGRAPHIC', 'build' => 'VARBINARY(30)'], //4
        ['db' => 'TIMESTAMP', 'meta' => 'TS', 'ff' => 'TS', 'output' => 'TIMESTAMP', 'build' => 'TIMESTAMP'], //5
        ['db' => 'DATE', 'meta' => 'D', 'ff' => 'D', 'output' => 'DATE', 'build' => 'DATE'], //6
        ['db' => 'TIME', 'meta' => 'T', 'ff' => 'T', 'output' => 'TIME', 'build' => 'TIME'],  //7
        ['db' => 'BLOB', 'meta' => 'B', 'ff' => 'B', 'output' => 'BLOB', 'build' => 'BLOB'], //8
        ['db' => 'CLOB', 'meta' => 'XL', 'ff' => 'XL', 'output' => 'CLOB', 'build' => 'CLOB'],  //9
        ['db' => 'DBCLOB', 'meta' => 'XL', 'ff' => 'XL', 'output' => 'CLOB', 'build' => 'DBCLOB'],  //10
        ['db' => 'BOOLEAN', 'meta' => 'L', 'ff' => 'L', 'output' => 'BOOLEAN', 'build' => 'BOOLEAN'], //11
        ['db' => 'SMALLINT', 'meta' => 'I2', 'ff' => 'I', 'output' => 'SMALLINT', 'build' => 'SMALLINT'], //12
        ['db' => 'INTEGER', 'meta' => 'I', 'ff' => 'I', 'output' => 'INTEGER', 'build' => 'INTEGER'], //12
        ['db' => 'BIGINT', 'meta' => 'I8', 'ff' => 'I', 'output' => 'BIGINT', 'build' => 'BIGINT'], //13
        ['db' => 'DECIMAL_5_3', 'meta' => 'N', 'ff' => 'N', 'output' => 'DECIMAL', 'build' => 'DECIMAL(6,3)'], //14
        ['db' => 'DECIMAL_16_2', 'meta' => 'N', 'ff' => 'N', 'output' => 'DECIMAL', 'build' => 'DECIMAL(16,2)'], //14
        ['db' => 'REAL', 'meta' => 'R', 'ff' => 'R', 'output' => 'REAL', 'build' => 'REAL'], //15
        ['db' => 'FLOAT', 'meta' => 'F', 'ff' => 'F', 'output' => 'FLOAT', 'build' => 'FLOAT' ] //16
    ];

    /**
     * A database specific create table statement that wraps the
     * build statements above
     *
     * @var string
     */
    public string $createTableWrapper = '
    CREATE TABLE metatype_test(
    id BIGINT,
    %s
    )';
}
