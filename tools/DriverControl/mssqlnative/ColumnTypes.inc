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
        ['db' => 'TEXT', 'meta' => 'X', 'output' => 'TEXT', 'build' => 'TEXT'],

        ['db' => 'NCHAR', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'NCHAR(10)'],
        ['db' => 'NVARCHAR', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'NVARCHAR(10)'],
        
        ['db' => 'BIT', 'meta' => 'L', 'output' => 'BOOLEAN', 'build' => 'BIT'],
        
        ['db' => 'TINYINT', 'meta' => 'I1', 'output' => 'BOOLEAN', 'build' => 'TINYINT'],
        ['db' => 'SMALLINT', 'meta' => 'I2', 'output' => 'SMALLINT', 'build' => 'SMALLINT'],
        ['db' => 'INTEGER', 'meta' => 'I', 'output' => 'INTEGER', 'build' => 'INTEGER'],
        ['db' => 'BIGINT', 'meta' => 'I8', 'output' => 'BIGINT', 'build' => 'BIGINT'],
      
        ['db' => 'FLOAT', 'meta' => 'F', 'output' => 'DOUBLE', 'build' => 'FLOAT'],
    
        ['db' => 'NUMERIC', 'meta' => 'N', 'output' => 'NUMERIC', 'build' => 'DECIMAL(16,2)'],
    
        ['db' => 'DATETIME2', 'meta' => 'T', 'output' => 'DATETIME', 'build' => 'DATETIME2'],
        ['db' => 'TIMESTAMP', 'meta' => 'T', 'output' => 'TIMESTAMP', 'build' => 'TIMESTAMP'],
        
        ['db' => 'DATE', 'meta' => 'D', 'output' => 'DATE', 'build' => 'DATE'],
        ['db' => 'TIME', 'meta' => 'T', 'output' => 'TIME', 'build' => 'TIME']
    
    ];

    /**
     * A database specific create table statement that wraps the 
     * build statements above
     *
     * @var string
     */
    public string $createTableWrapper = '
    CREATE TABLE metatype_test(
    id BIGINT IDENTITY(1,1),
    %s
    )';

}
