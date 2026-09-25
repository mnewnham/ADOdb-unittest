<?php

$coreController = sprintf(
    '%s/DriverControl/CoreControl.inc',
    $GLOBALS['unitTestToolsDirectory']
);
require_once $coreController;

class DriverControl extends \CoreControl
{
    
    public int $fetchBothManner = self::BOTH_MANNER_ALT_NUM_LAST;

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

    /**
     * Do updates to the data dictionary require transactional scope
     *
     * @var boolean
     */
    public bool $dictionaryRequireTransactions = true;
}
