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
    public string $qStrExpectedResult = "Famed author James O\\'Sullivan";
    
    /**
     * What additional data is required for column comments
     *
     * @var string
     */
    public string $columnCommentDefinitions = 'VARCHAR(20)';

    /**
     * Does the DB support ENUM natively
     *
     * @var boolean
     */
    public bool $hasNativeEnum = true;
}
