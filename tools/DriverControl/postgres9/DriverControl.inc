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
}
