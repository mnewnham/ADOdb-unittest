-- Creates Simple stored procedures for testing Meta and Core
-- Contains custom unittest SQL instructions

-- A Simple Procedure that returns a recordset
-- READ <<

CREATE OR REPLACE PROCEDURE SP_OUTPUT_TEST(IN filter_number INTEGER, OUT recordcount INTEGER)
AS 
BEGIN

    SELECT COUNT(*) INTO recordcount FROM testtable_3 WHERE number_run_field < filter_number;
  
END;
-- <<

