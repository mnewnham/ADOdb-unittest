<?php

/**
 * General setup include for session setup
 * This tests establishing a new connection
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

namespace MNewnham\ADOdbUnitTest\Session;

use MNewnham\ADOdbUnitTest\ADOdbTestCase;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class SessionSetup
 *
 * General Setup
 */
class SessionSetup extends ADOdbTestCase
{
    public static function setUpBeforeClass(): void
    {

        if ($GLOBALS['skipSessionTests'] == 1) {
            return;
        }
    }

    public function setup(): void
    {
        if ($GLOBALS['skipSessionTests'] == 1) {
            $this->markTestSkipped('Session testing is disabled');
            return;
        }
        if ($GLOBALS['ADOdbConnection']->database == ':memory:') {
            $this->markTestSkipped('Sessions cannot be tested using a memory based connection');
            return;
        }

        $sessionParams = $GLOBALS['TestingControl']['session'];
        $testParams    = $GLOBALS['sessionTestParameters'];
        foreach ($sessionParams as $key => $value) {
            if (!$value) {
                continue;
            }
            $key   = strtolower($key);
            $value = strtolower($value);

            if (in_array($key, ['url', 'verbose', 'skiptests', 'persist', 'debug'])) {
                continue;
            }

            switch ($key) {
            case 'compress':
                $lookups = array_keys($testParams['compression']);
                break;
            case 'encrypt': 
                $lookups = array_keys($testParams['encryption']);
                break;
            case 'clob':
                $lookups = ['clob', 'blob'];
                break;
            case 'horde':
                if (!is_dir($value)) {
                    $lookups = ['The directory holding the HORDE project'];
                } else {
                    $lookups = [$value];
                }
                break;
            default:
                $lookups = [];
            }

            if (!in_array($value, $lookups)) {
                $message = sprintf(
                    'Invalid value [%s] for key [%s]. Must be one of the following [%s]',
                    $value,
                    $key,
                    implode(', ', $lookups)
                );
                $this->markTestSkipped($message);
                return;

            }
        }

        parent::setup();
    }
}
