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
}
