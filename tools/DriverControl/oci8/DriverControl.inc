<?php

$coreController = sprintf(
    '%s/DriverControl/CoreControl.inc',
    $GLOBALS['unitTestToolsDirectory']
);
require_once $coreController;

class DriverControl extends \CoreControl{

    /**
	 * Indicates whether native mode casing is upper or lower
	 *
	 * @var integer
	 */
	public int $nativeModeCasing = 1;
}