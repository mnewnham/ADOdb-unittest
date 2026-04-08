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
     * If required, what is the stored procedure IN parameter type
     *
     * @var mixed
     */
    public mixed $inParameterType = OCI_B_INT;

    /**
     * If required, what is the stored procedure OUT parameter type
     *
     * @var mixed
     */
    public mixed $outParameterType = OCI_B_CURSOR;

    /**
     * For methods that test for databases, use schema instead
     *
     * @var mixed
     */
    public bool $databaseIsSchema = true;
}
