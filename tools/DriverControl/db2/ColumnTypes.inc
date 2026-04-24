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
        ['db' => 'CHAR', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'CHAR(10)'],              //0
        ['db' => 'VARCHAR', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'VARCHAR(10)'],        //1
        ['db' => 'VARCHAR', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'VARCHAR(1)'],        //1
        ['db' => 'BINARY', 'meta' => 'C2', 'output' => 'BINARY', 'build' => 'BINARY'],         //2
        ['db' => 'VARBINARY(30)', 'meta' => 'C2', 'output' => 'BINARY', 'build' => 'VARBINARY(30)'], //3
        ['db' => 'TIMESTAMP', 'meta' => 'TS', 'output' => 'TIMESTAMP', 'build' => 'TIMESTAMP'], //4
        ['db' => 'DATE', 'meta' => 'D', 'output' => 'DATE', 'build' => 'DATE'], //5
        ['db' => 'TIME', 'meta' => 'T', 'output' => 'TIME', 'build' => 'TIME'],  //6
        ['db' => 'BLOB', 'meta' => 'B', 'output' => 'BLOB', 'build' => 'BLOB'], //7
        ['db' => 'CLOB', 'meta' => 'XL', 'output' => 'CLOB', 'build' => 'CLOB'],  //8
        ['db' => 'DBCLOB', 'meta' => 'XL', 'output' => 'CLOB', 'build' => 'DBCLOB'],  //9
        ['db' => 'BOOLEAN', 'meta' => 'L', 'output' => 'BOOLEAN', 'build' => 'BOOLEAN'], //10
        ['db' => 'SMALLINT', 'meta' => 'I2', 'output' => 'SMALLINT', 'build' => 'SMALLINT'], //11
        ['db' => 'INTEGER', 'meta' => 'I', 'output' => 'INTEGER', 'build' => 'INTEGER'], //12
        ['db' => 'BIGINT', 'meta' => 'I8', 'output' => 'BIGINT', 'build' => 'BIGINT'], //13
        ['db' => 'DECIMAL(5,3)', 'meta' => 'N', 'output' => 'DECIMAL', 'build' => 'DECIMAL(6,3)'], //14
        ['db' => 'DECIMAL(16,2)', 'meta' => 'N', 'output' => 'DECIMAL', 'build' => 'DECIMAL(16,2)'], //14
        ['db' => 'REAL', 'meta' => 'R', 'output' => 'REAL', 'build' => 'REAL'], //15
        ['db' => 'FLOAT', 'meta' => 'F', 'output' => 'FLOAT', 'build' => 'FLOAT' ] //16
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
