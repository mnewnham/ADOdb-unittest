<?php

/**
 * Test data definitions for the mysqli driver of ADOdb.
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
 * Metatype Test cases for for the MySQLi native driver
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
        ['db' => 'CHAR', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'CHAR(10)'],
        ['db' => 'VARCHAR', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'VARCHAR(10)'],
        ['db' => 'BINARY', 'meta' => 'C2', 'output' => 'NVARCHAR', 'build' => 'BINARY(10)'],
        ['db' => 'VARBINARY', 'meta' => 'C2', 'output' => 'NVARCHAR', 'build' => 'VARBINARY(10)'],

        ['db' => 'TINYBLOB', 'meta' => 'B', 'output' => 'LONGBLOB', 'build' => 'TINYBLOB'],
        ['db' => 'BLOB', 'meta' => 'B', 'output' => 'LONGBLOB', 'build' => 'BLOB(1000)'],
        ['db' => 'MEDIUMBLOB', 'meta' => 'B', 'output' => 'LONGBLOB', 'build' => 'MEDIUMBLOB'],
        ['db' => 'LONGBLOB', 'meta' => 'B', 'output' => 'LONGBLOB', 'build' => 'LONGBLOB'],

        ['db' => 'TINYTEXT', 'meta' => 'X', 'output' => 'TEXT', 'build' => 'TINYTEXT'],
        ['db' => 'TEXT', 'meta' => 'X', 'output' => 'TEXT', 'build' => 'TEXT(1000)'],
        ['db' => 'MEDIUMTEXT', 'meta' => 'X', 'output' => 'TEXT', 'build' => 'MEDIUMTEXT'],
        ['db' => 'LONGTEXT', 'meta' => 'X', 'output' => 'TEXT', 'build' => 'LONGTEXT'],

        //['db' => 'ENUM', 'meta' => 'E', 'output' => 'ENUM', 'build' => "ENUM('cheddar','shropshire','wenslydale')"],
        //['db' => 'SET', 'meta' => 'E', 'output' => 'ENUM', 'build' => "SET('plaice','haddock','cod')"],
       
        ['db' => 'BIT', 'meta' => 'L', 'output' => 'BOOLEAN', 'build' => 'BIT(1)'],
        ['db' => 'BOOLEAN', 'meta' => 'L', 'output' => 'BOOLEAN', 'build' => 'BOOLEAN'],
        
        ['db' => 'TINYINT', 'meta' => 'I1', 'output' => 'BOOLEAN', 'build' => 'TINYINT(1)'],
        ['db' => 'SMALLINT', 'meta' => 'I2', 'output' => 'SMALLINT', 'build' => 'SMALLINT(10)'],
        ['db' => 'MEDIUMINT', 'meta' => 'I4', 'output' => 'INTEGER', 'build' => 'MEDIUMINT(10)'],
        ['db' => 'INTEGER', 'meta' => 'I', 'output' => 'INTEGER', 'build' => 'INTEGER(10)'],
        ['db' => 'BIGINT', 'meta' => 'I8', 'output' => 'BIGINT', 'build' => 'BIGINT(10)'],

        ['db' => 'FLOAT', 'meta' => 'F', 'output' => 'DOUBLE', 'build' => 'FLOAT(10)'],
        ['db' => 'BIGFLOAT', 'meta' => 'F', 'output' => 'DOUBLE', 'build' => 'FLOAT(26)'],
        ['db' => 'DOUBLE', 'meta' => 'F', 'output' => 'DOUBLE', 'build' => 'DOUBLE(14,12)'],
        ['db' => 'DOUBLE PRECISION', 'meta' => 'F', 'output' => 'DOUBLE', 'build' => 'DOUBLE PRECISION(16,12)'],

        ['db' => 'NUMERIC', 'meta' => 'N', 'output' => 'NUMERIC', 'build' => 'DECIMAL(16,2)'],

        ['db' => 'DATETIME', 'meta' => 'T', 'output' => 'DATETIME', 'build' => 'DATETIME'],
        ['db' => 'TIMESTAMP', 'meta' => 'T', 'output' => 'TIMESTAMP', 'build' => 'TIMESTAMP'],

        ['db' => 'DATE', 'meta' => 'D', 'output' => 'DATE', 'build' => 'DATE'],
        ['db' => 'TIME', 'meta' => 'T', 'output' => 'DATETIME', 'build' => 'TIME'],
        ['db' => 'YEAR', 'meta' => 'D', 'output' => 'DATE', 'build' => 'YEAR'],
  
    ];

    /**
     * A database specific create table statement that wraps the 
     * build statements above
     *
     * @var string
     */
    public string $createTableWrapper = '
    CREATE TABLE metatype_test(
    id INTEGER
    %s,
	PRIMARY KEY(id)
    )';

}
