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
    public int $fetchBothManner = 0;

    /**
     * What the qstr process should return as a quoted string
     *
     * @var string
     */
    public string $qStrExpectedResult = "Famed author James O''Sullivan";

    /**
     * How to obtain the entire contents of the datetime_field
     *
     * @var string
     */
    public string $dateField = 'date_field';

    /**
     * How to obtain the entire contents of the offsetdate_test_field
     *
     * @var string
     */
    public string $offsetDateTestField = "FORMAT(offsetdate_test_field, 'yyyy-MM-dd HH:mm')";


    /**
     * How to obtain the entire contents of the datetime_field
     *
     * @var string
     */
    public string $dateTimeField = "(FORMAT(datetime_field, 'yyyy-MM-dd HH:mm'))";

    //$dateFormat = "FORMAT(DATEADD(s, %s, %s), 'yyyy-MM-dd HH:mm')";
     /**
     * Converts a timestamp to a datetime string
     *
     * @var string
     */
    public string $dateTimeTranslation =  "FORMAT(CAST(%s AS DATETIME), 'yyyy-MM-dd HH:mm')";

    public function __construct()
    {
        $this->schemaOwner = $GLOBALS['schemaOwner'];
    }
}
