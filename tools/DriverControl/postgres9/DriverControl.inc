<?php

$coreController = sprintf(
    '%s/DriverControl/CoreControl.inc',
    $GLOBALS['unitTestToolsDirectory']
);
require_once $coreController;

class DriverControl extends \CoreControl
{
     /**
     * What the qstr process should return as a quoted string
     *
     * @var string
     */
    public string $qStrExpectedResult = "Famed author James O''Sullivan";

    /**
     * How to obtain the entire contents of the offsetdate_test_field
     *
     * @var string
     */
    public string $offsetDateTestField = "offsetdate_test_field";

    /**
     * Converts a timestamp to a datetime string
     *
     * @var string
     */
    public string $dateTimeTranslation =  "%s";

    public bool $supportsDropIfExists = true;
    
    public function __construct()
    {
        $this->schemaOwner = $GLOBALS['schemaOwner'];
    }
}
