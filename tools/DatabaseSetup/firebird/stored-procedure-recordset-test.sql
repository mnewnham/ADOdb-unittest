-- Creates Simple stored procedures for testing Meta and Core
-- Contains custom unittest SQL instructions
--  DROP PROCEDURE IF EXISTS SP_RECORDSET_TEST;

-- A Simple Procedure that returns a recordset
-- READ <<

CREATE OR REPLACE PROCEDURE sp_recordset_test(IN filter_number INTEGER)

RESULT SETS 1
LANGUAGE SQL
BEGIN
    DECLARE C1 CURSOR WITH RETURN TO CALLER FOR
    SELECT varchar_field, datetime_field,date_field, integer_field,decimal_field,number_run_field 
      FROM testtable_3 
     WHERE number_run_field < filter_number;

    OPEN C1;
END
-- <<
