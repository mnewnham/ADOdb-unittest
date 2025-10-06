<?php
/**
 * Tests cases for core move() methods on an empty recordset
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

require __DIR__ .  '/MoveTest.php';

/**
 * Class MoveTest
 *
 * Test cases for Move methods
 */
class MoveTestOnEmpty extends MoveTest
{
    protected string $setupSql = "SELECT * FROM testtable_3 WHERE id>9999 ORDER BY id";
}
