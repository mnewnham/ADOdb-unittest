<?php

/**
 * Tests cases for date functions of ADODb. Some of these functions
 * are effectively obsolete since 64 bit processors
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

namespace MNewnham\ADOdbUnitTest\DateFunctions;

use MNewnham\ADOdbUnitTest\DateFunctions\DateHandling;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class DateFunctions.
 *
 * Test cases for ADOdb date functions
 */
class DbDateTest extends DateHandling
{

    
    /**
     * Test for {@see ADOConnection::dbDate())
     *
     * @link https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:dbdate
     *
     * @return void
     *
     */
    public function testDbDate(): void
    {
        $today = date('Y-m-d');

        $dbDate =  $this->db->dbDate($today);
        list($errno, $errmsg) = $this->assertADOdbError('dbDate()');


        $this->assertNotNull(
            $dbDate,
            'dbDate() should return an SQL string to retrieve ' .
            'todays date in ISO format'
        );
    }

}
