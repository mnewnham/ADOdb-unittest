<?php

/**
 * ADOdb constants, classes and functions if not available from the main library
 *
 * The only purpose of this file is to reduce the number of invalid constant
 * definition warnings when looking at the source code.
 *
 * @package    adodb-unittest
 * @subpackage tools
 *
 */

if (!defined('ADODB_FORCE_IGNORE')) {
    define('ADODB_FORCE_IGNORE', 0);
    define('ADODB_FORCE_NULL', 1);
    define('ADODB_FORCE_EMPTY', 2);
    define('ADODB_FORCE_VALUE', 3);
    define('ADODB_FORCE_NULL_AND_ZERO', 4);

    define('ADODB_STRINGMAX_NOTSET', -1);
    define('ADODB_STRINGMAX_NOLIMIT', -2);

    define('ADODB_DEFAULT_METATYPE', 'N');

    define('ADODB_BAD_RS', '<p>Bad $rs in %s. Connection or SQL invalid. Try using $connection->debug=true;</p>');

    // allow [ ] @ ` " and . in table names
    define('ADODB_TABLE_REGEX', '([]0-9a-z_\:\"\`\.\@\[-]*)');

    define('ADODB_PREFETCH_ROWS', 10);

    define('ADODB_FETCH_DEFAULT', 0);
    define('ADODB_FETCH_NUM', 1);
    define('ADODB_FETCH_ASSOC', 2);
    define('ADODB_FETCH_BOTH', 3);

    define('ADODB_ASSOC_CASE_LOWER', 0);
    define('ADODB_ASSOC_CASE_UPPER', 1);
    define('ADODB_ASSOC_CASE_NATIVE', 2);

    define('TIMESTAMP_FIRST_YEAR', 100);

    define('DB_AUTOQUERY_INSERT', 1);
    define('DB_AUTOQUERY_UPDATE', 2);
}
if (!class_exists('adoSchema')) {
    class adoSchema
    {
        function __construct($db) {}
    }
}

if (!class_exists('ADOdb_Active_Record')) {
    class ADOdb_Active_Record{}
}

if (!function_exists('ADODB_SetDatabaseAdapter')) {
    function ADODB_SetDatabaseAdapter($db) {}
}

if (!function_exists('NewDataDictionary')) {
    function NewDataDictionary($db) {}
}

if (!function_exists('newAdoConnection')) {
    function newAdoConnection(): object { return new stdClass(); }
}