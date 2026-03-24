-- Creates Simple stored procedures for testing Meta and Core
-- Contains custom unittest SQL instructions

-- A Simple Procedure that returns a recordset
-- READ <<

CREATE OR REPLACE PROCEDURE SP_OUTPUT_TEST(IN filter_number INTEGER, OUT recordcount INTEGER)

LANGUAGE SQL
BEGIN
 -- Declare a variable to hold the count
    DECLARE V_COUNT INTEGER DEFAULT 0;

    SELECT COUNT(*) INTO V_COUNT FROM testtable_3 WHERE number_run_field < filter_number;

    -- Set the OUT parameter
    SET RECORDCOUNT = V_COUNT;
END
-- <<

