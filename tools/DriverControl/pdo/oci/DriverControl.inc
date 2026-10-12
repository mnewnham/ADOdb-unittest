<?php

$coreController = sprintf(
    '%s/DriverControl/CoreControl.inc',
    $GLOBALS['unitTestToolsDirectory']
);
require_once $coreController;

class DriverControl extends \CoreControl
{
    /**
     * Indicates whether native mode casing is upper or lower
     *
     * @var integer
     */
    public int $nativeModeCasing = 1;

    /**
     * The way in which the driver retrieves the fields in
     * FETCH_BOTH mode. Most drivers return the columns as
     * NUM,ASSOC,NUM,ASSOC. But others may return
     * ASSOC,NUM,ASSOC,NUM
     *
     * @var integer
     */
    public int $fetchBothManner = 1;


    /**
     * If required, what is the stored procedure IN parameter type
     *
     * @var mixed
     */
    //public mixed $inParameterType = OCI_B_INT;

    /**
     * If required, what is the stored procedure OUT parameter type
     *
     * @var mixed
     */
    //public mixed $outParameterType = OCI_B_CURSOR;

    /**
     * For methods that test for databases, use schema instead
     *
     * @var mixed
     */
    public bool $databaseIsSchema = true;

     /**
     * Does the driver support index comments
     *
     * @var boolean
     */
    public bool $supportsIndexComments = false;

    /**
     * What the qstr process should return as a quoted string
     *
     * @var string
     */
    public string $qStrExpectedResult = "Famed author James O''Sullivan";

    /**
     * A wrapper to allow the execution of date statements
     *
     * @var string
     */
    public $dateMethodExecutor = 'SELECT %s AS result FROM dual';

     /**
     * How to obtain the entire contents of the date_field
     *
     * @var string
     */
    public string $dateField = "TO_CHAR(date_field, 'YYYY-MM-DD HH24:MI:SS')";

    /**
     * How to obtain the entire contents of the datetime_field
     *
     * @var string
     */
    public string $dateTimeField = "TO_CHAR(datetime_field, 'YYYY-MM-DD HH24:MI:SS')";

    /**
     * How to obtain the entire contents of the offsetdate_test_field
     *
     * @var string
     */
    public string $offsetDateTestField = "TO_CHAR(offsetdate_test_field, 'YYYY-MM-DD HH24:MI:SS')";

     /**
     * Converts a timestamp to a datetime string
     *
     * @var string
     */
    public string $dateTimeTranslation =  "(TO_CHAR(%s, 'YYYY-MM-DD HH24:MI:SS'))";

    /**
     * Converts a timestamp to a date string
     *
     * @var string
     */
    public string $dateTranslation =  "(TO_CHAR(%s, 'YYYY-MM-DD'))";
}
