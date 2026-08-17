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
    public int $nativeModeCasing = 0;

    /**
     * What the qstr process should return as a quoted string
     *
     * @var string
     */
    public string $qStrExpectedResult = "Famed author James O''Sullivan";

    /**
     * Can compound primary keys include AUTO components
     *
     * @var boolean
     */
    public bool $allowAutoIncrementOnCompundKeys = false;

    /**
     * Does the driver support renaming a table
     *
     * @var boolean
     */
    public bool $supportsRenameTable = false;
}
