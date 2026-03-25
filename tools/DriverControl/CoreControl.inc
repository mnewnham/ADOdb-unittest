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
     * What additional data is required for column comments
     *
     * @var string
     */
    public string $columnCommentDefinitions = '';

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
}
