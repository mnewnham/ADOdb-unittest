<?php

$coreController = sprintf(
    '%s/DriverControl/CoreControl.inc',
    $GLOBALS['unitTestToolsDirectory']
);
require_once $coreController;

class DriverControl extends \CoreControl
{
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
     * The way in which the driver retrieves the fields in
     * FETCH_BOTH mode. Most drivers return the columns as
     * NUM,ASSOC,NUM,ASSOC. But others may return
     * ASSOC,NUM,ASSOC,NUM
     *
     * @var integer
     */
    public int $fetchBothManner = 1;

     /**
     * Do updates to comments require transactional scope
     *
     * @var boolean
     */
    public bool $commentsRequireTransactions = false;

    /**
     * Does the driver support index comments
     *
     * @var boolean
     */
    public bool $supportsIndexComments = false;

    /**
     * Do updates to the data dictionary require transactional scope
     *
     * @var boolean
     */
    public bool $dictionaryRequireTransactions = false;
}
