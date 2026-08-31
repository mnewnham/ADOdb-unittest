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
    public int $fetchBothManner = 0;
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

    /**
     * Does the driver support true internal sequences
     *
     * @var boolean
     */
    public bool $hasInternalSequences = false;
}
