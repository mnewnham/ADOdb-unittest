<?php

/**
 * Tests cases for the mysqli driver of ADOdb.
 * Try to write database-agnostic tests where possible. Use the
 * driver-specific include if not possible
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
namespace MNewnham\ADOdbUnitTest;

use PHPUnit\Framework\TestCase;


/**
 * Class MetaFunctionsTest
 *
 * Test cases for for ADOdb MetaFunctions
 */
class ADOdbTestCase extends TestCase
{
    protected ?object $db;
    protected ?string $adoDriver;
    protected ?object $dataDictionary;

    protected bool $skipFollowingTests = false;
    protected bool $skipAllTests       = false;

    protected string $testTableName = 'testtable_1';
    protected string $testIndexName1 = 'insertion_index_1';
    protected string $testIndexName2 = 'insertion_index_2';

    protected int $affectedRows = 0;
    /**
     * Starts a new ADOdb connection for each test. Use this
     * if the driver is buggy and throws too many errors. This
     * flushes the error out of the driver
     *
     * @var boolean
     */
    protected bool $createNewConnection = false;

    protected array $caseDescription = array(
        ADODB_ASSOC_CASE_UPPER => 'ADODB_ASSOC_CASE_UPPER',
        ADODB_ASSOC_CASE_LOWER => 'ADODB_ASSOC_CASE_LOWER',
        ADODB_ASSOC_CASE_NATIVE => 'ADODB_ASSOC_CASE_NATIVE'
    );

    protected array $testFetchModes = [
        ADODB_FETCH_NUM   => '[1] ADODB_FETCH_NUM',
        ADODB_FETCH_ASSOC => '[2] ADODB_FETCH_ASSOC',
        ADODB_FETCH_BOTH  => '[3] ADODB_FETCH_BOTH'
    ];

    /**
     * Instantiates new ADOdb connection to flush every test
     *
     * @return object
     */
    public function establishDatabaseConnector(): object
    {

        $template = array(
            'dsn' => '',
            'host' => null,
            'user' => null,
            'password' => null,
            'database' => null,
            'parameters' => null,
            'debug' => 0
        );


        $credentials = array_merge(
            $template,
            $GLOBALS['TestingControl'][$GLOBALS['loadDriver']]
        );

        $loadDriver = str_replace('pdo-', 'PDO\\', $GLOBALS['loadDriver']);

        $db = newAdoConnection($loadDriver);
        $db->debug = $credentials['debug'];

        if ($credentials['parameters']) {
            $p = explode(';', $credentials['parameters']);
            $p = array_filter($p);
            foreach ($p as $param) {
                $scp = explode('=', $param);
                if (preg_match('/^[0-9]+$/', $scp[0])) {
                    $scp[0] = (int)$scp[0];
                }
                if (preg_match('/^[0-9]+$/', $scp[1])) {
                    $scp[1] = (int)$scp[1];
                }

                $db->setConnectionParameter($scp[0], $scp[1]);
            }
        }

        if ($credentials['dsn']) {
            $db->connect(
                $credentials['dsn'],
                $credentials['user'],
                $credentials['password'],
                $credentials['database']
            );
        } else {
            $db->connect(
                $credentials['host'],
                $credentials['user'],
                $credentials['password'],
                $credentials['database']
            );
        }

        if (!$db->isConnected()) {
            die(
                sprintf(
                    '%s database connection not established',
                    $GLOBALS['adoDriver']
                )
            );
        }

        return $db;
    }

    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setup(): void
    {

        $this->adoDriver      = $GLOBALS['ADOdriver'];

        if ($this->createNewConnection) {
            $this->db             = $this->establishDatabaseConnector();
            $this->dataDictionary = NewDataDictionary($this->db);
        } else {
            $this->db             = $GLOBALS['ADOdbConnection'];
            $this->dataDictionary = $GLOBALS['ADOdataDictionary'];
        }

        $GLOBALS['testTableName']   = $this->testTableName;
        $GLOBALS['testIndexName1']  = $this->testIndexName1;
        $GLOBALS['testIndexName2']  = $this->testIndexName2;
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
     * Exwcutes an SQL statement within a transaction and returns
     * the result plus any message if it fails
     *
     * @param string     $sql          The SQL to execute
     * @param array|null $bind         Optional bind parameters
     * @param bool       $transactions Optional Use transactions
     *
     * @return array
     */
    public function executeSqlString(
        string $sql,
        ?array $bind = null,
        bool $transactions = true
    ): array {

        $db = $this->db;

        if ($transactions) {
            $db->startTrans();
        }
        if ($bind) {
            $result = $db->execute($sql, $bind);
        } else {
            $result = $db->execute($sql);
        }

        $errno  = $db->errorNo();
        $errmsg = $db->errorMsg();

        $this->affectedRows = $db->Affected_Rows();

        if ($transactions) {
            $db->completeTrans();
        }

        $params = '';
        if ($bind) {
            $params = ' [' . implode(' , ', $bind) . ']';
        }

        $this->assertEquals(
            0,
            $errno,
            sprintf(
                'ADOdb string execution of SQL %s%s ' .
                'should not return error: %d - %s',
                $sql,
                $params,
                $errno,
                $errmsg
            )
        );

        return array($result,$errno,$errmsg);
    }

    /**
     * Returns the number of affected rows, if any
     *
     * @return integer
     */
    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }

    /**
     * Tests an ADOdb execution for db errors
     *
     * @param string $sql         The statement executed
     * @param ?array $bind        Optional Bind
     * @param bool   $expectError Should an error be thrown
     *
     * @return array
     */
    public function assertADOdbError(
        string $sql,
        mixed $bind = false,
        bool $expectError = false
    ): array {


        $db = $this->db;

        $errno  = $db->errorNo();
        $errmsg = $db->errorMsg();

        $db->_errorCode = 0;
        $db->_errorMsg = '';

        $transOff = $db->transOff;


        $params = '';
        if ($bind) {
            $params = ' [' . implode(' , ', $bind) . ']';
        }

        if ($expectError) {
            $this->assertNotEquals(
                0,
                $errno,
                sprintf(
                    'ADOdb execution of SQL %s%s should return error: %d - %s',
                    $sql,
                    $params,
                    $errno,
                    $errmsg
                )
            );
        } else {
            $this->assertEquals(
                0,
                $errno,
                sprintf(
                    'ADOdb execution of SQL %s%s should not return error: %d - %s',
                    $sql,
                    $params,
                    $errno,
                    $errmsg
                )
            );
        }

        if ($GLOBALS['globalTransOff'] < $transOff) {
            $this->assertTrue(
                $transOff < 2,
                sprintf(
                    '$transOff should not exceed 1 in test suite, currently %d, previously %d',
                    $transOff,
                    $GLOBALS['globalTransOff']
                )
            );
        }
        $GLOBALS['globalTransOff'] = $transOff;

        return array($errno,$errmsg);
    }

    /**
     * Exwcutes an SQL statement within a transaction and returns
     * the result plus any message if it fails
     *
     * @param array      $sqlArray The SQL to execute
     * @param array|null $bind     Optional bind parameters
     *
     * @return array
     */
    public function executeDictionaryAction(
        array $sqlArray,
        ?array $bind = null
    ): array {

        $db = $this->db;
        $dictionary = $this->dataDictionary;

        $db->startTrans();

        if ($bind) {
            $result = $dictionary->executeSqlArray($sqlArray, $bind);
        } else {
            $result = $dictionary->executeSqlArray($sqlArray);
        }

        $errno  = $db->errorNo();
        $errmsg = $db->errorMsg();

        $db->completeTrans();


        $params = '';
        if ($bind) {
            $params = ' [' . implode(' , ', $bind) . ']';
        }

        $this->assertEquals(
            0,
            $errno,
            sprintf(
                'ADOdb array execution of SQL %s%s should not return error: %d - %s',
                implode('/', $sqlArray),
                $params,
                $errno,
                $errmsg
            )
        );

        return array($result,$errno,$errmsg);
    }

    /**
     * We don't know what format the data will be returned in ADODB_FETCH_BOTH
     * requests, numeric key first then associative or vice-versa. This
     * method sorts them to all numeric keysJ followed by all associative keys.
     * That way we can get a standardized data set for comparisons
     *
     * @param array $inputArray The data to sort
     *
     * @return array
     */
    protected function sortFetchBothRecords(array $inputArray): array
    {
        $outputArray         = array();

        foreach ($inputArray as $k => $v) {
            $outputArray[$k] = ksort($v);
        }

        return $outputArray;
    }
}
