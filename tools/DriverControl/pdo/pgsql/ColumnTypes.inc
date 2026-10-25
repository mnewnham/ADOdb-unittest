<?php

/**
 * Test data definitions for the PDO\pgsql driver of ADOdb.
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
 * Metatype Test cases for for the PDO Postgres driver
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
        ['db' => 'CHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR', 'build' => 'CHAR(10)'],
        ['db' => 'VARCHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR', 'build' => 'VARCHAR(10)'],
       // ['db' => 'BINARY', 'meta' => 'C2', 'ff' => 'C', 'output' => 'NVARCHAR', 'build' => 'BINARY(10)'],
        //['db' => 'VARBINARY', 'meta' => 'C2', 'ff' => 'C', 'output' => 'NVARCHAR', 'build' => 'VARBINARY(10)'],
        ['db' => 'TEXT', 'meta' => 'X', 'ff' => 'X', 'output' => 'TEXT', 'build' => 'TEXT'],
        ['db' => 'BYTEA', 'meta' => 'B', 'ff' => 'B', 'output' => 'BYTEA', 'build' => 'BYTEA'],

        ['db' => 'NCHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR', 'build' => 'NCHAR(10)'],
        //['db' => 'NVARCHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR', 'build' => 'NVARCHAR(10)'],

        ['db' => 'BIT', 'meta' => 'B', 'ff' => 'B', 'output' => 'BYTEA', 'build' => 'BIT'],

       // ['db' => 'TINYINT', 'meta' => 'I1', 'ff' => 'C', 'output' => 'BOOLEAN', 'build' => 'TINYINT'],
        ['db' => 'SMALLINT', 'meta' => 'I2', 'ff' => 'I', 'output' => 'INT2', 'build' => 'SMALLINT'],
        ['db' => 'INTEGER', 'meta' => 'I', 'ff' => 'I', 'output' => 'INT4', 'build' => 'INTEGER'],
        ['db' => 'BIGINT', 'meta' => 'I8', 'ff' => 'I', 'output' => 'INT8', 'build' => 'BIGINT'],

        ['db' => 'FLOAT', 'meta' => 'F', 'ff' => 'F', 'output' => 'FLOAT8', 'build' => 'FLOAT'],

        ['db' => 'NUMERIC', 'meta' => 'N', 'ff' => 'N', 'output' => 'NUMERIC', 'build' => 'NUMERIC(16,2)'],

        //['db' => 'DATETIME', 'meta' => 'T', 'ff' => 'C', 'output' => 'DATETIME', 'build' => 'DATETIME'],
        ['db' => 'TIMESTAMP', 'meta' => 'TS', 'ff' => 'TS', 'output' => 'TIMESTAMP', 'build' => 'TIMESTAMP'],

        ['db' => 'DATE', 'meta' => 'D', 'ff' => 'D', 'output' => 'DATE', 'build' => 'DATE'],
        ['db' => 'TIME', 'meta' => 'T', 'ff' => 'T', 'output' => 'TIME', 'build' => 'TIME'],
        ['db' => 'SERIAL', 'meta' => 'S', 'ff' => 'I', 'output' => 'SERIAL', 'build' => 'SERIAL'],

    ];

    /**
     * A database specific create table statement that wraps the
     * build statements above
     *
     * @var string
     */
    public string $createTableWrapper = '
    CREATE TABLE metatype_test(
   	id SERIAL PRIMARY KEY,
    %s
    )';
}
