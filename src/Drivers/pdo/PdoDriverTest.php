<?php

/**
 * Tests cases for the PDO driver of ADOdb.
 * the driver specific test, e.g. pdo-sqlite, pdo-mysql, etc are also run
 * Try to write database-agnostic tests where possible.
 * This test does not support the legacy PDO drivers
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

namespace MNewnham\ADOdbUnitTest\Drivers;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class PdoDriverTest
 *
 * Test cases for the ADOdb PDO Drivers
 */
class PdoDriverTest extends ADOdbTestCase
{
    const BIND_USE_QUESTION_MARKS = 0;
    const BIND_USE_NAMED_PARAMETERS = 1;
    const BIND_USE_BOTH = 2;

    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        parent::setup();

        if (substr($this->adoDriver, 0, 3) !== 'pdo') {
            $this->skipFollowingTests = true;
            $this->markTestSkipped(
                'This test is only applicable for PDO drivers'
            );
        }
    }

    /**
     * Tear down the test environment
     *
     * @return void
     */
    public function tearDown(): void
    {
    }

    /**
     * Test for {@see ADODB_pdo#containsQuestionMarkPlaceholder)
     *
     */
    #[DataProvider('providerContainsQuestionMarkPlaceholder')]
    public function testContainsQuestionMarkPlaceholder($result, $sql): void
    {
        $method = new \ReflectionMethod($this->db, 'containsQuestionMarkPlaceholder');

        //$pdoDriver = new ADODB_pdo();
        $this->assertSame($result, $method->invoke($this->db, $sql));
    }

    /**
     * Data provider for {@see testContainsQuestionMarkPlaceholder()}
     *
     * @return array [result, SQL statement]
     */
    public static function providerContainsQuestionMarkPlaceholder(): array
    {
        return [
            [true, 'SELECT * FROM employees WHERE emp_no = ?;'],
            [true, 'SELECT * FROM employees WHERE emp_no = ?'],
            [true, 'SELECT * FROM employees WHERE emp_no=?'],
            [true, 'SELECT * FROM employees WHERE emp_no>?'],
            [true, 'SELECT * FROM employees WHERE emp_no<?'],
            [true, 'SELECT * FROM employees WHERE emp_no>=?'],
            [true, 'SELECT * FROM employees WHERE emp_no<=?'],
            [true, 'SELECT * FROM employees WHERE emp_no<>?'],
            [true, 'SELECT * FROM employees WHERE emp_no!=?'],
            [true, 'SELECT * FROM employees WHERE emp_no IN (?)'],
            [true, 'SELECT * FROM employees WHERE emp_no=`?` OR emp_no=?'],
            [true, 'UPDATE employees SET emp_name=? WHERE emp_no=?'],

            [false, 'SELECT * FROM employees'],
            [false, 'SELECT * FROM employees WHERE emp_no=`?`'],
            [false, 'SELECT * FROM employees WHERE emp_no=??'],
            [false, 'SELECT * FROM employees WHERE emp_no=:emp_no'],
        ];
    }

    /**
     * Test for {@see ADODB_pdo#conformToBindParameterStyle)
     *
     */
     #[DataProvider('providerConformToBindParameterStyle')]
    public function testConformToBindParameterStyle(
        $expected,
        $inputarr,
        $bindParameterStyle,
        $sql
    ): void {

         $method = new \ReflectionMethod($this->db, 'conformToBindParameterStyle');


         $this->db->bindParameterStyle = $bindParameterStyle;
         $this->assertSame($expected, $method->invoke($this->db, $sql ?? '', $inputarr));
    }

    /**
     * Data provider for {@see testConformToBindParameterStyle()}
     *
     * @return array [expected, inputarr, bindParameterStyle, SQL statement]
     */
    public static function providerConformToBindParameterStyle(): array
    {

        return [
            [
                [1, 2, 3],
                [1, 2, 3],
                self::BIND_USE_QUESTION_MARKS,
                null
            ],
            [
                [1, 2, 3],
                ['a' => 1, 'b' => 2, 'c' => 3],
                self::BIND_USE_QUESTION_MARKS,
                null
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
                self::BIND_USE_NAMED_PARAMETERS,
                null
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => 1, 'b' => 2, 'c' => 3],
                self::BIND_USE_NAMED_PARAMETERS,
                null
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
                self::BIND_USE_BOTH,
                'SELECT * FROM employees WHERE emp_no = ?'
            ],
            [
                [1, 2, 3],
                ['a' => 1, 'b' => 2, 'c' => 3],
                self::BIND_USE_BOTH,
                'SELECT * FROM employees WHERE emp_no = ?'
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
                self::BIND_USE_BOTH,
                'SELECT * FROM employees WHERE emp_no = :id'
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => 1, 'b' => 2, 'c' => 3],
               self::BIND_USE_BOTH,
                'SELECT * FROM employees WHERE emp_no = :id'
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
                9999,   // Incorrect values result in default behavior.
                null
            ],
            [
                [1, 2, 3],
                ['a' => 1, 'b' => 2, 'c' => 3],
                9999,   // Incorrect values result in default behavior.
                null
            ],
        ];
    }
}
