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
        ['db' => 'VARCHAR2', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'VARCHAR2(10)'],
        ['db' => 'NCHAR', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'NCHAR(10)'],
        ['db' => 'NVARCHAR2', 'meta' => 'C', 'output' => 'VARCHAR', 'build' => 'NVARCHAR2(10)'],
        ['db' => 'NVARCHAR2', 'meta' => 'TYPEX', 'output' => 'TYPEX', 'build' => 'NVARCHAR2(1000)'],

        ['db' => 'TIMESTAMP', 'meta' => 'TS', 'output' => 'TIMESTAMP', 'build' => 'TIMESTAMP'],
        ['db' => 'DATE', 'meta' => 'T', 'output' => 'DATE', 'build' => 'DATE'],
       
        ['db' => 'BLOB', 'meta' => 'B', 'output' => 'BLOB', 'build' => 'BLOB'],
        ['db' => 'CLOB', 'meta' => 'XL', 'output' => 'CLOB', 'build' => 'CLOB'],
        ['db' => 'NCLOB', 'meta' => 'XL', 'output' => 'CLOB', 'build' => 'NCLOB'],
        ['db' => 'BFILE', 'meta' => 'XL', 'output' => 'CLOB', 'build' => 'BFILE'],
       
        ['db' => 'LONG', 'meta' => 'TYPEX', 'output' => 'VARCHAR(4000)', 'build' => 'LONG'],
        ['db' => 'LONGRAW', 'meta' => 'TYPEX', 'output' =>'VARCHAR(4000)', 'build' => 'RAW(1000)'],


        ['db' => 'NUMBER(1)', 'meta' => 'L', 'output' => 'NUMBER(1)', 'build' => 'NUMBER(1)'],
        ['db' => 'BOOLEAN', 'meta' => 'L', 'output' => 'BOOLEAN', 'build' => 'NUMBER(1)'],
        
        ['db' => 'NUMBER(3)', 'meta' => 'I1', 'output' => 'NUMBER(3)', 'build' => 'NUMBER(3)'],
        ['db' => 'NUMBER(5)', 'meta' => 'I2', 'output' => 'NUMBER(5)', 'build' => 'NUMBER(5)'],
        ['db' => 'NUMBER(10)', 'meta' => 'I4', 'output' => 'NUMBER(10)', 'build' => 'NUMBER(10)'],
        ['db' => 'NUMBER(20)', 'meta' => 'I', 'output' => 'NUMBER(20)', 'build' => 'NUMBER(20)'],
        ['db' => 'NUMBER(38)', 'meta' => 'I8', 'output' => 'NUMBER(38)', 'build' => 'NUMBER(38)'],

        ['db' => 'NUMBER(16,2)', 'meta' => 'N', 'output' => 'NUMBER', 'build' => 'NUMBER(16,2)'],

        ['db' => 'FLOAT(126)', 'meta' => 'F', 'output' => 'FLOAT(126)', 'build' => 'FLOAT(126)'],
        ['db' => 'FLOAT(63)', 'meta' => 'R', 'output' => 'FLOAT(63)', 'build' => 'FLOAT(63)'],
  
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
