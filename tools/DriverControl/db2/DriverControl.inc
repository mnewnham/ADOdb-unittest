<?php

$coreController = sprintf(
    '%s/DriverControl/CoreControl.inc',
    $GLOBALS['unitTestToolsDirectory']
);
require_once $coreController;

class DriverControl extends \CoreControl
{
    /**
     * The way in which the driver retrieves the fields in
     * FETCH_BOTH mode. Most drivers return the columns as
     * NUM,ASSOC,NUM,ASSOC. DB2 returns
     * ASSOC,NUM,ASSOC,NUM
     *
     * @var integer
     */
    public int $fetchBothManner = 1;

    /**
     * Indicates whether native mode casing is upper or lower
     *
     * @var integer
     */
    public int $nativeModeCasing = 1;

     /**
     * What the qstr process should return as a quoted string
     *
     * @var string
     */
    public string $qStrExpectedResult = "Famed author James O''Sullivan";

    public function __construct()
    {
        $this->schemaOwner = $GLOBALS['schemaOwner'];
    }

    /**
     * A wrapper to allow the execution of date statements
     *
     * @var string
     */
    public $dateMethodExecutor = 'SELECT %s AS result FROM SYSIBM.SYSDUMMY1';

    /**
     * For methods that test for databases, use schema instead
     *
     * @var mixed
     */
    public bool $databaseIsSchema = true;
}
