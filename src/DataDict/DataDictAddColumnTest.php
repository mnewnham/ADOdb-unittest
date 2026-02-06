<?php

/**
 * Tests cases for Dictionary Add Column functions of ADODb
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

use MNewnham\ADOdbUnitTest\DataDict\DataDictFunctions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class DataDictAddColumnTest
 *
 * Test cases for for ADOdb DataDictAddColumn
 */
class DataDictAddColumnTest extends DataDictFunctions
{
    /**
     * Global setup for the test class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {

        parent::setUpBeforeClass();
    }

    
    /**
     * Test for {@see ADODConnection::addColumnSQL()}
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:dictionary:addcolumnsql
     *
     * @return void
     */
    public function testaddColumnToBasicTable(): void
    {

       
        $flds = " 
            NEW_VARCHAR_FIELD C(50) NOTNULL DEFAULT '',
            NEW_DATE_FIELD D NOTNULL DEFAULT '2010-01-01',
            NEW_INTEGER_FIELD I4 NOTNULL DEFAULT 0,
            NEW_BOOLEAN_FIELD L NOTNULL DEFAULT 0,
            NEW_DECIMAL_FIELD N(8.4) DEFAULT 0,
            NEW_DROPPABLE_FIELD N(10.6) DEFAULT 80.111
            ";

        $sqlArray = $this->dataDictionary->AddColumnSQL($this->testTableName, $flds);

        list ($response,$errno,$errmsg) = $this->executeDictionaryAction($sqlArray);

        $GLOBALS['baseTestsComplete'] = true;

        if ($errno > 0) {
            if ($GLOBALS['baseTestsComplete'] == false) {
                $this->skipFollowingTests = true;
            }
            return;
        }

        $metaColumns = $this->db->metaColumns($this->testTableName);

        $this->assertArrayHasKey(
            'VARCHAR_FIELD',
            $metaColumns,
            'Test of AddColumnSQL'
        );

        if (!array_key_exists('VARCHAR_FIELD', $metaColumns)) {
            $this->skipFollowingTests = true;
        }
    }
}
