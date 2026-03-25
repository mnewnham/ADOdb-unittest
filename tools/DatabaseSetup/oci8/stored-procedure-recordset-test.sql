-- Creates Simple stored procedures for testing Meta and Core
-- Contains custom unittest SQL instructions
--  DROP PROCEDURE IF EXISTS SP_RECORDSET_TEST;

-- A Simple Procedure that returns a recordset
-- READ <<

CREATE OR REPLACE PROCEDURE sp_recordset_test(
    filter_number IN INTEGER,
    C1 OUT SYS_REFCURSOR)

AS BEGIN
    OPEN C1 FOR
    SELECT varchar_field, datetime_field,date_field, integer_field,decimal_field,number_run_field 
      FROM testtable_3 
     WHERE number_run_field < filter_number;

END;
-- <<
