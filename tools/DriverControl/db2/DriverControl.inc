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
    
    /**
     * How to obtain the entire contents of the date_field
     *
     * @var string
     */
    public string $dateField = "TO_CHAR(date_field, 'YYYY-MM-DD HH24:MI')";

    /**
     * How to obtain the entire contents of the sqldate_test_field
     *
     * @var string
     */
    public string $sqlDateTestField = "TO_CHAR(sqldate_test_field, 'YYYY-MM-DD HH24:MI')";

    /**
     * How to obtain the entire contents of the offsetdate_test_field
     *
     * @var string
     */
    public string $offsetDateTestField = "TO_CHAR(offsetdate_test_field, 'YYYY-MM-DD HH24:MI')";


}
