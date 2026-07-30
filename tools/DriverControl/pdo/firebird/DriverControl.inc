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
     * NUM,ASSOC,NUM,ASSOC. But others may return
     * ASSOC,NUM,ASSOC,NUM
     *
     * @var integer
     */
    public int $fetchBothManner = 1;

    
    /**
     * What the qstr process should return as a quoted string
     *
     * @var string
     */
    public string $qStrExpectedResult = "Famed author James O\\'Sullivan";

    /**
     * Does the driver support renaming a table
     *
     * @var boolean
     */
    public bool $supportsRenameTable = false;

     /**
     * Does the driver support the IF EXISTS statement
     *
     * @var boolean
     */
    public bool $supportsDropIfExists = false;


    public function __construct()
    {
        $this->schemaOwner = $GLOBALS['schemaOwner'];
    }
}
