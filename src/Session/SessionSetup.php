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

    protected $curlVerbose = 0;

    public function transmitSessionTest(
        string $file,
        string $class,
        string $test,
        string $testData,
        ?array $optionalHeaders = array(),
        ?array $debugTrace = array()
    ) {

        $sessionParameters = [
            'skipTests' => 0,
            'url' => 'http://localhost/unittest',
            'verbose => 0'
        ];

        $method = 'GET';
        $postFields = [
            'file'  => $file,
            'class' => $class,
            'test'  => $test,
            'data'  => $testData
        ];

        $data = base64_encode(json_encode($postFields));

        if (array_key_exists('session', $GLOBALS['TestingControl'])) {
            $storedParameters = $GLOBALS['TestingControl']['session'];
            $sessionParameters = array_merge($sessionParameters, $storedParameters);
        }

        $cu = curl_init();

        $headerArray = [];

        foreach ($optionalHeaders as $oh) {
            $headerArray[] = $oh;
        }


        $curl_options = array(
            CURLOPT_URL            => sprintf('%s?data=%s', $sessionParameters['url'], $data),
            CURLOPT_VERBOSE        => $sessionParameters['verbose'],
            CURLOPT_USERAGENT      => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => $headerArray

        );

        if ($method != 'POST') {
            /*
            * If we are using a custom methed, we still pass the values in using the postfields

            $curl_options[CURLOPT_CUSTOMREQUEST] = $method;
            if ($postFields)
            {
                $curl_options[CURLOPT_POSTFIELDS] = $postFields;
            }
                */
        } else {
            $curl_options[CURLOPT_POST] = 1;
            $curl_options[CURLOPT_POSTFIELDS] = $postFields;
        }

        curl_setopt_array($cu, $curl_options);

        $curlOutput    = curl_exec($cu);
        $info          = curl_getinfo($cu);
        $curlResponse  = $info['http_code'];

        if ($curlResponse == 200) {
        }


        if ($sessionParameters['verbose']) {
            /*
            * Add class debuggong
            */
            print "\n-------------- INPUT METHOD IS $method -----------\n";
            print "\n-------------- HEADERS -----------\n";
            print_r($headerArray);
            print "\n-------------- POSTDATA -----------\n";
            print_r($postFields);

            print "\n-------------- OUTPUT -----------\n";
            print $curlOutput;
            print "\n-------------- INFO -----------\n";
            print_r($info);
        }

        return array($curlResponse, $curlOutput);
    }
}
