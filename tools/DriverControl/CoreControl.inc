<?php

class CoreControl
{
    /**
     * Represents how the Both array is constructed
     */
    const BOTH_MANNER_ALT_NUM_FIRST   = 0;
    const BOTH_MANNER_ALT_NUM_LAST    = 1;

    /**
     * The way in which the driver retrieves the fields in
     * FETCH_BOTH mode. Most drivers return the columns as
     * NUM,ASSOC,NUM,ASSOC. But others may return
     * ASSOC,NUM,ASSOC,NUM
     *
     * @var integer
     */
    public int $fetchBothManner = 0;

    /*
    * Represents how Native mode data is returned
    */
    const NATIVE_IS_LOWERCASE = 0;
    const NATIVE_IS_UPPERCASE = 1;

    /**
     * Indicates whether native mode casing is upper or lower
     *
     * @var integer
     */
    public int $nativeModeCasing = 0;

    /**
     * Do updates to the data dictionary require transactional scope
     *
     * @var boolean
     */
    public bool $dictionaryRequireTransactions = true;


    /**
     * Do updates to comments require transactional scope
     *
     * @var boolean
     */
    public bool $commentsRequireTransactions = true;

    /**
     * What additional data is required for column comments
     *
     * @var string
     */
    public string $columnCommentDefinitions = '';

    /**
     * Does the driver support index comments
     *
     * @var boolean
     */
    public bool $supportsIndexComments = true;

    /**
     * Does the DB support ENUM natively
     *
     * @var boolean
     */
    public bool $hasNativeEnum = false;

    
    /**
     * If required, what is the stored procedure IN parameter type
     *
     * @var mixed
     */
    public mixed $inParameterType = false;

    /**
     * If required, what is the stored procedure OUT parameter type
     *
     * @var mixed
     */
    public mixed $outParameterType = false;

    /**
     * For methods that test for ownership, what is the schema owner
     *
     * @var mixed
     */
    public mixed $schemaOwner = false;

    /**
     * For methods that test for databases, use schema instead
     *
     * @var mixed
     */
    public bool $databaseIsSchema = false;

    /**
     * What the qstr process should return as a quoted string
     *
     * @var string
     */
    public string $qStrExpectedResult = "Famed author James O\\'Sullivan";


}
