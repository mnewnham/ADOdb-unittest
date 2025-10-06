<?php
/**
 * Tests cases for core SQL functions of ADODb
 * 
 * This file is part of ADOdb-unittest, a PHPUnit test suite for 
 * the ADOdb Database Abstraction Layer library for PHP.
 *
 * PHP version 8.0.0+
 * 
 * @category  Library
 * @package   ADOdb-unittest
 * @author    Mark Newnham <mnewnham@github.com>
 * @copyright 2025 Mark Newnham, Damien Regad and the ADOdb community
 * @license   MIT https://en.wikipedia.org/wiki/MIT_License
 * 
 * @link https://github.com/adodb-unittest This projects home site
 * @link https://adodb.org ADOdbProject's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 */

use PHPUnit\Framework\TestCase;
use ADOdbUnitTest\CoreModule;

/**
 * ClassGetOneTest
 *
 * Test cases for for ADOdb Core functions
 */
class GetMedianTest extends ADOdbCoreSetup
{
    
     
    /**
     * Test for {@see ADODConnection::getMedian()]
     *
     * @param int    $expectedValue The expected value to be returned
     * @param string $table         Table name
     * @param string $column        Table column
     * @param string $where         An optional criteria
     * 
     * @return void
     * 
     * @dataProvider providerTestGetMedian
     * 
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:getmedian
     */
    public function testGetMedian(
        mixed $expectedValue, 
        string $table, 
        string $column,
        mixed $where
    ): void {
    
        
        foreach ($this->testFetchModes as $fetchMode=>$fetchDescription) {
        
            $this->db->startTrans();
            
            $this->db->setFetchMode($fetchMode);

            $actualValue = $this->db->getMedian($table, $column, $where);

            list($errno,$errmsg) = $this->assertADOdbError('getMedian()');
            $this->db->completeTrans();
            
            $this->assertSame(
                $expectedValue, 
                $actualValue,
                sprintf('[%s] Test of getMedian()', $fetchDescription)
            );

        }

    }

    /**
     * Data provider for {@see testGetMedian()}
     *
     * @return array [
     *   string expected value, 
     *   string table
     *   string column
     *   string where
     *   ]
     */
    public function providerTestGetMedian(): array
    {
        
        return [
            'Return testtable_3, number_run_field' => [
                '6', 
                'testtable_3',
                'number_run_field',
               null
            ],
            'Return testtable_3, number_run_field,id>4' => [
                '8', 
                'testtable_3',
                'number_run_field',
               'WHERE id>4'
            ],
            'Return testtable_3, number_run_field, id<0' => [
                false, 
                'testtable_3',
                'number_run_field',
               'WHERE id<0'
            ],
            'Return testtable_3, varchar_field' => [
                'LINE 4', 
                'testtable_3',
                'varchar_field',
               'WHERE id>0 ORDER BY varchar_field'
            ],
        ];
    }
}