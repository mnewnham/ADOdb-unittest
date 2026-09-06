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

    /**
     * How to obtain the entire contents of the offsetdate_test_field
     *
     * @var string
     */
    public string $offsetDateTestField = "DATE_FORMAT(offsetdate_test_field, '%Y-%m-%d %H:%i')";

    /**
     * Converts a timestamp to a datetime string
     *
     * @var string
     */
    public string $dateTimeTranslation =  "DATE_FORMAT(%s, '%%Y-%%m-%%d %%H:%%i')";
}
