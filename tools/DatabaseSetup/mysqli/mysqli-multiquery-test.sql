-- MySQL Specific test for multiple recordset
DROP PROCEDURE IF EXISTS adodb_test_multi_recordsets;
-- Custom unittest SQL reads entire statement as single execution
-- READ <<
CREATE PROCEDURE adodb_test_multi_recordsets()
    LANGUAGE SQL
    NOT DETERMINISTIC
    SQL SECURITY DEFINER
    BEGIN
    SELECT "a" row1_1,
           "b" row1_2,
           "c" row1_3;
    SELECT "123" row2_1,
           "234" row2_2;
    SELECT 1 row3_1,
           null row3_2,
           3 row3_3,
           '' row3_4;
    END;
-- <<
