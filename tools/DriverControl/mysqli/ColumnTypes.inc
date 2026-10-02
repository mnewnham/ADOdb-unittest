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
        ['db' => 'CHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR', 'build' => 'CHAR(10)'],
        ['db' => 'VARCHAR', 'meta' => 'C', 'ff' => 'C', 'output' => 'VARCHAR', 'build' => 'VARCHAR(10)'],
        ['db' => 'BINARY', 'meta' => 'C2', 'ff' => 'C2', 'output' => 'NVARCHAR', 'build' => 'BINARY(10)'],
        ['db' => 'VARBINARY', 'meta' => 'C2', 'ff' => 'C2', 'output' => 'NVARCHAR', 'build' => 'VARBINARY(10)'],
        ['db' => 'TINYBLOB', 'meta' => 'B', 'ff' => 'B', 'output' => 'LONGBLOB', 'build' => 'TINYBLOB'],
        ['db' => 'BLOB', 'meta' => 'B', 'ff' => 'B', 'output' => 'LONGBLOB', 'build' => 'BLOB(1000)'],
        ['db' => 'MEDIUMBLOB', 'meta' => 'B', 'ff' => 'B', 'output' => 'LONGBLOB', 'build' => 'MEDIUMBLOB'],
        ['db' => 'LONGBLOB', 'meta' => 'B', 'ff' => 'B', 'output' => 'LONGBLOB', 'build' => 'LONGBLOB'],
        ['db' => 'TINYTEXT', 'meta' => 'X', 'ff' => 'X', 'output' => 'TEXT', 'build' => 'TINYTEXT'],
        ['db' => 'TEXT', 'meta' => 'X', 'ff' => 'X', 'output' => 'TEXT', 'build' => 'TEXT(1000)'],
        ['db' => 'MEDIUMTEXT', 'meta' => 'X', 'ff' => 'X', 'output' => 'TEXT', 'build' => 'MEDIUMTEXT'],
        ['db' => 'LONGTEXT', 'meta' => 'X', 'ff' => 'X', 'output' => 'TEXT', 'build' => 'LONGTEXT'],
        ['db' => 'ENUM', 'meta' => 'ENUM', 'ff' => 'ENUM', 'output' => 'ENUM', 'build' => "ENUM('cheddar','shropshire','wenslydale')"], //254
        ['db' => 'SET', 'meta' => 'ENUM', 'ff' => 'ENUM', 'output' => 'ENUM', 'build' => "SET('plaice','haddock','cod')"], //254
        ['db' => 'BIT', 'meta' => 'BIT', 'ff' => 'N', 'output' => 'BIT', 'build' => 'BIT(1)'],
        ['db' => 'BOOLEAN', 'meta' => 'L', 'ff' => 'L', 'output' => 'BOOLEAN', 'build' => 'BOOLEAN'],
        ['db' => 'TINYINT', 'meta' => 'L', 'ff' => 'L', 'output' => 'BOOLEAN', 'build' => 'TINYINT(1)'],
        ['db' => 'SMALLINT', 'meta' => 'I2', 'ff' => 'I', 'output' => 'SMALLINT', 'build' => 'SMALLINT(10)'],
        ['db' => 'MEDIUMINT', 'meta' => 'I', 'ff' => 'I', 'output' => 'INTEGER', 'build' => 'MEDIUMINT(10)'],
        ['db' => 'INTEGER', 'meta' => 'I', 'ff' => 'I', 'output' => 'INTEGER', 'build' => 'INTEGER(10)'],
        ['db' => 'BIGINT', 'meta' => 'I8', 'ff' => 'I', 'output' => 'BIGINT', 'build' => 'BIGINT(10)'],
        ['db' => 'FLOAT', 'meta' => 'F', 'ff' => 'F', 'output' => 'DOUBLE', 'build' => 'FLOAT(10)'],
        ['db' => 'BIGFLOAT', 'meta' => 'F', 'ff' => 'F', 'output' => 'DOUBLE', 'build' => 'FLOAT(26)'],
        ['db' => 'DOUBLE', 'meta' => 'F', 'ff' => 'F', 'output' => 'DOUBLE', 'build' => 'DOUBLE(14,12)'],
        ['db' => 'DOUBLE PRECISION', 'meta' => 'F', 'ff' => 'F', 'output' => 'DOUBLE', 'build' => 'DOUBLE PRECISION(16,12)'],
        ['db' => 'NUMERIC', 'meta' => 'N', 'ff' => 'N', 'output' => 'NUMERIC', 'build' => 'DECIMAL(16,2)'],
        ['db' => 'DATETIME', 'meta' => 'TS', 'ff' => 'TS', 'output' => 'TIMESTAMP', 'build' => 'DATETIME'],
        ['db' => 'TIMESTAMP', 'meta' => 'TS', 'ff' => 'TS', 'output' => 'TIMESTAMP', 'build' => 'TIMESTAMP'],
        ['db' => 'DATE', 'meta' => 'D', 'ff' => 'D', 'output' => 'DATE', 'build' => 'DATE'],
        ['db' => 'TIME', 'meta' => 'T', 'ff' => 'T', 'output' => 'TIMESTAMP', 'build' => 'TIME'],
        ['db' => 'YEAR', 'meta' => 'D', 'ff' => 'D', 'output' => 'DATE', 'build' => 'YEAR'],

    ];

    /**
     * A database specific create table statement that wraps the
     * build statements above
     *
     * @var string
     */
    public string $createTableWrapper = '
    CREATE TABLE metatype_test(
    id INT NOT NULL AUTO_INCREMENT,
    %s,
    PRIMARY KEY(id)
    ) ENGINE=INNODB;';
}
