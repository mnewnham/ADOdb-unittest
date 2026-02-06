<?php

/**
 * Base Test class for Data Dictionary functions of ADODb
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

namespace MNewnham\ADOdbUnitTest\DataDict;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MetaFunctions
 *
 * Test cases for for ADOdb MetaFunctions
 */
class DataDictFunctions extends ADOdbTestCase
{
    protected bool $skipCommentTests = false;

    protected string $testTableName = 'dictionary_change_test_table';
    protected string $testIndexName1 = 'insertion_index_1';
    protected string $testIndexName2 = 'insertion_index_2';

    /**
     * Sets up a flag used from refreshing the table mid-test
     *
     * @return void
     */
    public static function setupBeforeClass(): void
    {
        $GLOBALS['baseTestsComplete'] = 0;
    }

    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();
    }
}
